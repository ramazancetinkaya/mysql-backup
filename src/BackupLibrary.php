<?php
/**
 * MySQL Backup & Restore Library
 *
 * This library provides functionalities for backing up and restoring MySQL databases.
 * 
 * @category Library
 * @package  MySQLBackup
 * @author   Ramazan Çetinkaya <ramazancetinkayadev@hotmail.com>
 * @version  1.0
 * @license  MIT License
 * @link     https://github.com/ramazancetinkaya/mysql-backup
 */

namespace DatabaseBackupManager;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MySQLBackup
{

    /**
     * The PDO database connection instance
     * @var PDO
     */
    private $db;

    /**
     * The directory to store backup files
     * @var string
     */
    private string $backupFolder;

    /**
     * Constructor to initialize PDO connection.
     * 
     * @param PDO $db PDO instance for database connection
     * @param string $backupFolder Path to the backup folder
     */
    public function __construct(PDO $db, $backupFolder = 'backup') 
    {
        $this->db = $db;
        $this->backupFolder = rtrim($backupFolder, '/') . '/';
        $this->checkBackupFolder();

        // Check if ZipArchive class is available
        if (!class_exists('ZipArchive')) {
            throw new Exception('ZipArchive class not found. Please enable the Zip module in your PHP configuration.');
        }
    }

    /**
     * Check if the backup folder exists and has appropriate permissions.
     * If the folder does not exist, attempt to create it with appropriate permissions.
     * If the folder exists but does not have write permissions, attempt to set the permissions.
     *
     * @throws Exception If unable to create or set permissions for the backup folder.
     */
    private function checkBackupFolder(): void
    {
        // Check if the backup folder exists
        if (!file_exists($this->backupFolder)) 
        {
            // If the folder does not exist, attempt to create it
            if (!mkdir($this->backupFolder, 0755, true)) 
            {
                // If unable to create the folder, throw an exception
                throw new Exception('Failed to create backup folder.');
            }
        }

        // Check if the backup folder is writable
        if (!is_writable($this->backupFolder)) 
        {
            // If the folder is not writable, attempt to set the permissions
            if (!chmod($this->backupFolder, 0755)) 
            {
                // If unable to set permissions, throw an exception
                throw new Exception('Failed to set write permissions for backup folder.');
            }
        }
    }

    /**
     * Backup the database tables.
     * 
     * @param array|string|null $tables Names of the tables to backup. If null, all tables will be backed up.
     * @param bool $includeData Whether to include table data in the backup.
     * @param bool $archive Whether to archive the backup file.
     * @param string|null $emailRecipient Email address to send the backup file.
     * @return string Path to the generated backup file.
     * @throws Exception If backup process fails.
     */
    public function backup($tables = null, $includeData = true, $archive = false, $emailRecipient = null) 
    {
        try {
            // Disable foreign key checks during backup
            $this->db->exec('SET foreign_key_checks = 0');

            // Start transaction to prevent any changes during backup
            $this->db->beginTransaction();

            // Generate backup file name
            $backupFileName = $this->generateBackupFileName($tables);

            // Open backup file for writing
            $backupFile = fopen($backupFileName, 'w');

            // Write header information to the backup file
            $this->writeBackupHeader($backupFile);

            // Backup tables
            if ($tables) {
                $this->backupTables($tables, $includeData, $backupFile);
            } else {
                $this->backupAllTables($includeData, $backupFile);
            }

            // Close the backup file
            fclose($backupFile);

            // If archive option is enabled, zip the backup file
            if ($archive) 
            {
                $backupFileName = $this->archiveBackupFile($backupFileName);
            }

            // If email recipient is provided, send the backup file
            if ($emailRecipient) 
            {
                $this->sendBackupByEmail($backupFileName, $emailRecipient);
            }

            // Commit transaction
            $this->db->commit();

            return $backupFileName;
        } catch (Exception $e) {
            // Rollback transaction on failure
            $this->db->rollBack();
            throw $e;
        } finally {
            // Re-enable foreign key checks
            $this->db->exec('SET foreign_key_checks = 1');
        }
    }

    /**
     * Restore the database from a backup file.
     * 
     * @param string $backupFilePath Path to the backup file.
     * @param bool $dropTables Whether to drop existing tables before restoring data. Default is true.
     * @throws Exception If restore process fails.
     */
    public function restore($backupFilePath, $dropTables = true) {
        try {
            // Begin transaction
            $this->db->beginTransaction();

            // Drop tables if requested
            if ($dropTables) {
                // Extract table names from backup file
                $tables = $this->extractTableNames($backupFilePath);

                // Drop tables from the database
                $this->dropTables($tables);
            }

            // Read backup file
            $backupContent = file_get_contents($backupFilePath);

            // Execute backup content as SQL queries
            $queries = explode(';', $backupContent);
            foreach ($queries as $query) {
                if (trim($query) !== '') {
                    $this->db->exec($query);
                }
            }

            // Commit transaction
            $this->db->commit();

            return true;
        } catch (Exception $e) {
            // Rollback transaction on failure
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Extract table names from the backup file.
     * 
     * @param string $backupFilePath Path to the backup file.
     * @return array Table names extracted from the backup file.
     */
    private function extractTableNames($backupFilePath) {
        $backupContent = file_get_contents($backupFilePath);
        preg_match_all('/Table structure for table `(\w+)`/', $backupContent, $matches);
        return $matches[1];
    }

    /**
     * Drop tables from the database.
     * 
     * @param array $tables Table names to be dropped.
     * @throws Exception If dropping tables fails.
     */
    private function dropTables($tables) {
        foreach ($tables as $table) {
            $this->db->exec("DROP TABLE IF EXISTS `$table`");
        }
    }

    /**
     * Generate backup file name.
     * 
     * @param array|string|null $tables Names of the tables to backup.
     * @return string Backup file name.
     */
    private function generateBackupFileName($tables) 
    {
        $dbName = $this->db->query('SELECT DATABASE()')->fetchColumn();
        $fileName = $this->backupFolder . 'backup_' . $dbName . ($tables ? '-' . implode('_', (array) $tables) : '') . '-' . date('Y-m-d_His') . '.sql';
        return $fileName;
    }

    /**
     * Write backup header information to the backup file.
     * 
     * @param resource $backupFile File handle of the backup file.
     */
    private function writeBackupHeader($backupFile) 
    {
        fwrite($backupFile, "-- Database Backup Manager\n");
        fwrite($backupFile, "-- This backup was created automatically by the Database Backup Manager\n");
        fwrite($backupFile, "-- © " . date('Y') . " Ramazan Çetinkaya. All rights reserved.\n");
        fwrite($backupFile, "-- https://github.com/ramazancetinkaya/mysql-backup\n");
        fwrite($backupFile, "--\n");
        fwrite($backupFile, "-- Host: " . $this->db->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n");
        fwrite($backupFile, "-- Generated on: " . date('Y-m-d H:i:s') . "\n");
        fwrite($backupFile, "-- Server version: " . $this->db->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n\n");
    }

    /**
     * Backup specified tables.
     * 
     * @param array|string $tables Names of the tables to backup.
     * @param bool $includeData Whether to include table data in the backup.
     * @param resource $backupFile File handle of the backup file.
     */
    private function backupTables($tables, $includeData, $backupFile) 
    {
        foreach ((array) $tables as $table) {
            $this->backupTableStructure($table, $backupFile);
            if ($includeData) {
                $this->backupTableData($table, $backupFile);
            }
        }
    }

    /**
     * Backup all tables in the database.
     * 
     * @param bool $includeData Whether to include table data in the backup.
     * @param resource $backupFile File handle of the backup file.
     */
    private function backupAllTables($includeData, $backupFile) 
    {
        $stmt = $this->db->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $this->backupTables($tables, $includeData, $backupFile);
    }

    /**
     * Backup table structure.
     * 
     * @param string $tableName Name of the table to backup.
     * @param resource $backupFile File handle of the backup file.
     */
    private function backupTableStructure($tableName, $backupFile) 
    {
        $stmt = $this->db->prepare("SHOW CREATE TABLE $tableName");
        $stmt->execute();
        $tableStructure = $stmt->fetch(PDO::FETCH_ASSOC);
        fwrite($backupFile, "--\n-- Table structure for table `$tableName`\n--\n\n");
        fwrite($backupFile, $tableStructure['Create Table'] . ";\n\n");
    }

    /**
     * Backup table data.
     * 
     * @param string $tableName Name of the table to backup.
     * @param resource $backupFile File handle of the backup file.
     */
    private function backupTableData($tableName, $backupFile) {
        $stmt = $this->db->prepare("SELECT * FROM $tableName");
        $stmt->execute();
        $tableData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($tableData)) {
            fwrite($backupFile, "--\n-- No data found for table `$tableName`\n--\n\n");
            return;
        }
        
        fwrite($backupFile, "--\n-- Dumping data for table `$tableName`\n--\n\n");
        fwrite($backupFile, "INSERT INTO `$tableName` (");
        $fields = array_keys($tableData[0]);
        fwrite($backupFile, "`" . implode("`, `", $fields) . "`");
        fwrite($backupFile, ") VALUES\n");
        foreach ($tableData as $row) {
            fwrite($backupFile, "(");
            $values = array_map(function($value) {
                return "'" . addslashes($value) . "'";
            }, array_values($row));
            fwrite($backupFile, implode(", ", $values));
            fwrite($backupFile, "),\n");
        }
        // Remove the trailing comma and newline character from the last row
        fseek($backupFile, -2, SEEK_END);
        fwrite($backupFile, ";\n\n");
        fwrite($backupFile, "-- End of database backup process\n");
    }

    /**
     * Archive backup file.
     * 
     * @param string $backupFileName Path to the backup file.
     * @return string Path to the archived backup file.
     * @throws Exception If archiving fails.
     */
    private function archiveBackupFile($backupFileName) 
    {
        $zipFileName = $backupFileName . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($backupFileName, basename($backupFileName));
            $zip->close();
            unlink($backupFileName);
            return $zipFileName;
        } else {
            throw new Exception('Failed to create zip archive.');
        }
    }

    /**
     * Send backup file by email.
     * 
     * @param string $backupFileName Path to the backup file.
     * @param string $recipient Email address of the recipient.
     * @throws Exception If sending email fails.
     */
    private function sendBackupByEmail($backupFileName, $recipient) 
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.example.com'; // Gmail SMTP: smtp.gmail.com
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your-email@example.com';
            $mail->Password   = 'your-email-password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('from@example.com', 'Database Backup Manager');
            $mail->addAddress($recipient);

            // Attach backup file
            $mail->addAttachment($backupFileName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Database Backup';
            $mail->Body    = '
                <html>
                <body>
                    <p>Hello,</p>
                    <p>Please find attached the database backup file you requested.</p>
                    <p>This backup was created automatically by the <a href="https://github.com/ramazancetinkaya/mysql-backup/">Database Backup Manager</a>.</p>
                </body>
                </html>
            ';
            $mail->send();
        } catch (Exception $e) {
            throw new Exception('Backup email could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        }
    }
}
