# MySQL Backup Library

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Version](https://img.shields.io/badge/version-1.0.0-green.svg)](https://github.com/ramazancetinkaya/mysql-backup)

A powerful PHP library designed to seamlessly create backups of MySQL databases using PDO, providing developers with a reliable and secure solution. 

<div align="center">
    If you find this project helpful or useful, please consider giving it a ⭐️ star on GitHub! Your support is greatly appreciated.
</div>

## Features

- **Effortless MySQL Database Backups**: Seamlessly create backups of MySQL databases with table structure and data preservation.
- **Secure and Reliable**: Built on PDO, this library ensures secure database connections and robust backup operations.
- **Modern and Future-Proof**: Utilizes PHP 8 features and follows contemporary coding practices for enhanced performance, readability, and maintainability.
- **Easy Integration**: Simple and intuitive interface enables developers to quickly integrate the library into their projects.
- **Extensible and Customizable**: Flexible architecture allows for easy customization and adaptation to different backup requirements.
- **Comprehensive Error Handling**: Detailed error messages and exception handling provide a robust backup solution.
- **Highly Documented**: Extensive documentation guides developers through installation, usage, and customization.

## Screenshot

![Screenshot](https://i.imgur.com/AEp4wJg.png)

## Usage

To use the "MySQL Backup" library, you will need to follow these steps:

1) First, make sure you have PHP installed on your system and have the PDO extension enabled. You should also have a MySQL database available.

2) Include the library file in your PHP script using the `require_once` statement:
```php
require_once 'MySQLBackup.php';
```

3) Create a PDO instance to establish a connection with your MySQL database. You'll need to provide the appropriate database credentials. Here's an example:
```php
$dsn = 'mysql:host=localhost;dbname=my_database';
$username = 'root';
$password = 'password';

$pdo = new PDO($dsn, $username, $password);
```

4) Create an instance of the `MySQLBackup` class, passing the PDO instance as a parameter:
```php
$backup = new MySQLBackup($pdo);
```

5) Call the `createBackup()` method to create a backup of your MySQL database. Provide the database name and the path where you want to save the backup file:
```php
$database = 'my_database';
$backupFile = '/path/to/backup.sql';

try {
    $success = $backup->createBackup($database, $backupFile);
    if ($success) {
        echo "Backup created successfully!";
    } else {
        echo "Backup creation failed.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

Make sure to replace `'my_database'` with the name of your actual database and `'/path/to/backup.sql'` with the desired path and filename for your backup file.

When you run the script, it will create a backup file at the specified location with the structure and data of your MySQL database.

Remember to handle any potential exceptions or errors that may occur during the backup creation process.

## Contributing
Contributions are welcome! If you find any issues or have suggestions for improvement, please open an issue or submit a pull request.

## License

This project is licensed under the [MIT] License - see the LICENSE.md file for details.

## Authors

**Ramazan Çetinkaya**
- <https://github.com/ramazancetinkaya>

## Copyright

Copyright (c) [2023] [Ramazan Çetinkaya]
