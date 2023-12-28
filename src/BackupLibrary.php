<?php

/**
 * MySQL Backup & Restore Library
 *
 * This library provides functionality for backing up and restoring MySQL databases using PDO.
 *
 * @category  Library
 * @package   BackupLibrary
 * @author    Ramazan Çetinkaya
 * @license   MIT License <https://opensource.org/licenses/MIT>
 * @version   1.0.0
 * @link      https://github.com/ramazancetinkaya/mysql-backup
 */

namespace ramazancetinkaya;
use PDO;

/**
 * Backup and restore MySQL databases.
 *
 * @class BackupLibrary
 */
class BackupLibrary
{
    /**
     * PDO instance for the database connection.
     *
     * @var PDO
     */
    private $pdo;

    /**
     * BackupLibrary constructor.
     *
     * @param PDO $pdo PDO instance for the database connection.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Performs a backup of the specified database and saves it to a SQL file.
     *
     * @param string $backupPath Path where the backup file should be saved.
     * @return bool True if the backup is successful, false otherwise.
     * @throws PDOException If an error occurs during the backup process.
     */
    public function backupDatabase(string $backupPath): bool
    {
        $backupFile = $this->generateBackupFilename($backupPath);

        try {
            $this->createBackupDirectory($backupPath);
            $this->writeBackupHeader($backupFile);
            $this->backupTables($backupFile);
            $this->writeBackupFooter($backupFile);

            return true;
        } catch (PDOException $e) {
            // Handle the exception or log the error
            throw $e;
        }

        return false;
    }

    /**
     * Restores a MySQL database from a SQL backup file.
     *
     * @param string $backupFile Path to the SQL backup file.
     * @return bool True if the restore is successful, false otherwise.
     * @throws PDOException If an error occurs during the restore process.
     */
    public function restoreDatabase(string $backupFile): bool
    {
        try {
            $this->disableForeignKeyChecks();
            $this->executeSqlFile($backupFile);
            $this->enableForeignKeyChecks();

            return true;
        } catch (PDOException $e) {
            // Handle the exception or log the error
            throw $e;
        }

        return false;
    }

    /**
     * Generates a unique backup filename based on the current date and time.
     *
     * @param string $backupPath Path where the backup file should be saved.
     * @return string The generated backup filename.
     */
    private function generateBackupFilename(string $backupPath): string
    {
        $filename = 'backup_' . date('Ymd_His') . '.sql';
        return rtrim($backupPath, '/') . '/' . $filename;
    }

    /**
     * Creates the backup directory if it doesn't exist.
     *
     * @param string $backupPath Path where the backup file should be saved.
     */
    private function createBackupDirectory(string $backupPath): void
    {
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
    }

    /**
     * Writes the backup file header.
     *
     * @param string $backupFile Path to the backup file.
     */
    private function writeBackupHeader(string $backupFile): void
    {
        $header = "-- MySQL Backup\n";
        $header .= "-- https://github.com/ramazancetinkaya/mysql-backup\n";
        $header .= "--\n";
        $header .= "-- Host: " . $this->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";
        $header .= "-- Generation Time: " . date('Y-m-d H:i:s') . "\n";
        $header .= "-- Server Version: " . $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
        $header .= "-- PHP Version: " . phpversion() . "\n\n";

        file_put_contents($backupFile, $header, FILE_APPEND);
    }

    /**
     * Writes the backup file footer.
     *
     * @param string $backupFile Path to the backup file.
     */
    private function writeBackupFooter(string $backupFile): void
    {
        $footer = "-- End of MySQL Backup.\n";
        $footer .= "-- Thank you for using the MySQL Backup library!\n";
        $footer .= "-- Ramazan Çetinkaya <ramazancetinkayadev@outlook.com>\n";
        $footer .= "-- https://github.com/ramazancetinkaya\n";
    
        file_put_contents($backupFile, $footer, FILE_APPEND);
    }

    /**
     * Performs backup of all tables in the database.
     *
     * @param string $backupFile Path to the backup file.
     */
    private function backupTables(string $backupFile): void
    {
        $tables = $this->getDatabaseTables();

        foreach ($tables as $table) {
            $this->backupTable($table, $backupFile);
        }
    }

    /**
     * Performs backup of a specific table in the database.
     *
     * @param string $table Table name.
     * @param string $backupFile Path to the backup file.
     */
    private function backupTable(string $table, string $backupFile): void
    {
        $sql = "SELECT * FROM `$table`";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            $this->writeTableBackupToFile($table, $rows, $backupFile);
        }
    }

    /**
     * Writes table data to the backup file.
     *
     * @param string $table Table name.
     * @param array $rows Table rows.
     * @param string $backupFile Path to the backup file.
     */
    private function writeTableBackupToFile(string $table, array $rows, string $backupFile): void
    {
        $handle = fopen($backupFile, 'a');
        if ($handle) {
            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "-- Table structure for table `$table`\n");
            fwrite($handle, "-- --------------------------------------------------------\n\n");

            // Get table structure and write it to the backup file
            $structure = $this->getTableStructure($table);
            fwrite($handle, $structure);

            fwrite($handle, "\n\n");
            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "-- Data for table `$table`\n");
            fwrite($handle, "-- --------------------------------------------------------\n\n");

            // Write table rows to the backup file
            foreach ($rows as $row) {
                $rowValues = $this->escapeRowValues($row);
                $insertStatement = "INSERT INTO `$table` VALUES (" . implode(', ', $rowValues) . ");\n";
                fwrite($handle, $insertStatement);
            }

            fwrite($handle, "\n");
            fclose($handle);
        }
    }

    /**
     * Retrieves the structure of a specific table.
     *
     * @param string $table Table name.
     * @return string Table structure as SQL statements.
     */
    private function getTableStructure(string $table): string
    {
        $sql = "SHOW CREATE TABLE `$table`";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== false) {
            return $result['Create Table'] . ";\n";
        }

        return '';
    }

    /**
     * Disables foreign key checks.
     */
    private function disableForeignKeyChecks(): void
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    }

    /**
     * Enables foreign key checks.
     */
    private function enableForeignKeyChecks(): void
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Executes an SQL file.
     *
     * @param string $backupFile Path to the SQL backup file.
     */
    private function executeSqlFile(string $backupFile): void
    {
        $sql = file_get_contents($backupFile);
        $this->pdo->exec($sql);
    }

    /**
     * Retrieves the list of tables in the database.
     *
     * @return array List of table names.
     */
    private function getDatabaseTables(): array
    {
        $sql = "SHOW TABLES";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Escapes row values to prevent SQL injection.
     *
     * @param array $row Table row.
     * @return array Escaped row values.
     */
    private function escapeRowValues(array $row): array
    {
        foreach ($row as &$value) {
            if ($value === null) {
                $value = 'NULL';
            } else {
                $value = $this->pdo->quote($value);
            }
        }

        return $row;
    }
}
