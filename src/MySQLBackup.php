<?php
/**
 * MySQL Backup Library
 *
 * A powerful and modern library for backing up MySQL databases.
 *
 * @category  Library
 * @package   MySQLBackup
 * @author    Ramazan Çetinkaya
 * @license   MIT License <https://opensource.org/licenses/MIT>
 * @version   1.0.0
 * @link      https://github.com/ramazancetinkaya/mysql-backup
 */

declare(strict_types=1);

/**
 * The main backup class.
 *
 * @class MySQLBackup
 */
class MySQLBackup
{
    /**
     * PDO instance for database connection.
     *
     * @var PDO
     */
    private $pdo;

    /**
     * MySQLBackup constructor.
     *
     * @param PDO $pdo PDO instance for database connection.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a backup of a MySQL database and save it to a file.
     *
     * @param string $database   Database name.
     * @param string $backupFile Path to save the backup file.
     *
     * @return bool True if backup creation is successful, false otherwise.
     * @throws Exception If backup file cannot be created or database backup fails.
     */
    public function createBackup(string $database, string $backupFile): bool
    {
        try {
            // Check if the backup file is writable
            if (!is_writable(dirname($backupFile))) {
                throw new Exception("Backup file is not writable.");
            }

            // Get a list of tables in the database
            $tables = $this->getTables($database);

            // Open the backup file for writing
            $handle = fopen($backupFile, 'w');

            // Write the backup header
            fwrite($handle, "-- MySQL Backup\n");
            fwrite($handle, "-- Database: {$database}\n");
            fwrite($handle, "-- Generated on: " . date('Y-m-d H:i:s') . "\n");
            fwrite($handle, "-- Coded by github.com/ramazancetinkaya\n\n");

            // Backup each table
            foreach ($tables as $table) {
                fwrite($handle, "-- -------------------------------------------------\n");
                fwrite($handle, "-- Table structure for table `{$table}`\n");
                fwrite($handle, "-- -------------------------------------------------\n\n");

                // Retrieve table structure
                $tableStructure = $this->getTableStructure($database, $table);
                fwrite($handle, $tableStructure . ";\n\n");

                fwrite($handle, "-- -------------------------------------------------\n");
                fwrite($handle, "-- Data for table `{$table}`\n");
                fwrite($handle, "-- -------------------------------------------------\n\n");

                // Retrieve table data
                $tableData = $this->getTableData($database, $table);
                fwrite($handle, $tableData . ";\n\n");
            }

            // Close the backup file
            fclose($handle);

            return true;
        } catch (Exception $e) {
            throw new Exception("Backup creation failed: " . $e->getMessage());
        }
    }

    /**
     * Get the list of tables in a database.
     *
     * @param string $database Database name.
     *
     * @return array Array of table names.
     */
    private function getTables(string $database): array
    {
        $query = "SHOW TABLES FROM `{$database}`";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get the CREATE TABLE statement for a table.
     *
     * @param string $database Database name.
     * @param string $table    Table name.
     *
     * @return string CREATE TABLE statement.
     */
    private function getTableStructure(string $database, string $table): string
    {
        $query = "SHOW CREATE TABLE `{$database}`.`{$table}`";
        $stmt = $this->pdo->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['Create Table'];
    }

    /**
     * Get the INSERT statements for table data.
     *
     * @param string $database Database name.
     * @param string $table    Table name.
     *
     * @return string INSERT statements.
     */
    private function getTableData(string $database, string $table): string
    {
        $query = "SELECT * FROM `{$database}`.`{$table}`";
        $stmt = $this->pdo->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $insertStatements = [];
        foreach ($result as $row) {
            // Escape and quote each column value
            $row = array_map([$this->pdo, 'quote'], $row);

            // Build the INSERT statement
            $insertStatements[] = "INSERT INTO `{$database}`.`{$table}` (" .
                implode(', ', array_keys($row)) .
                ") VALUES (" .
                implode(', ', $row) .
            ")";
        }

        return implode(";\n", $insertStatements);
    }
}
