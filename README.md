<h1 align="center">MySQL Backup & Restore Library</h1>

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Latest Version](https://img.shields.io/github/v/release/ramazancetinkaya/mysql-backup)](https://github.com/ramazancetinkaya/mysql-backup/releases)
![PHP](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg)
[![GitHub Issues](https://img.shields.io/github/issues/ramazancetinkaya/mysql-backup.svg)](https://github.com/ramazancetinkaya/mysql-backup/issues)
[![GitHub Forks](https://img.shields.io/github/forks/ramazancetinkaya/mysql-backup.svg)](https://github.com/ramazancetinkaya/mysql-backup/network)
[![GitHub Stars](https://img.shields.io/github/stars/ramazancetinkaya/mysql-backup.svg)](https://github.com/ramazancetinkaya/mysql-backup/stargazers)

<p align="center">
  <a href="https://github.com/ramazancetinkaya/mysql-backup">
    <img src="https://www.mysql.com/common/logos/logo-mysql-170x115.png" alt="Logo">
  </a>

  <h3 align="center">MySQL Backup & Restore Library</h3>

  <p align="center">
    A powerful and modern PHP library for backing up and restoring MySQL databases using PDO.
    <br>
    <a href="https://github.com/ramazancetinkaya/mysql-backup/blob/main/README.md"><strong>Explore the docs »</strong></a>
    <br>
    <br>
    <a href="https://github.com/ramazancetinkaya/mysql-backup/issues">Report a Bug</a>
    ·
    <a href="https://github.com/ramazancetinkaya/mysql-backup/pulls">New Pull Request</a>
  </p>
</p>

<br>

<p align="center">
  You have <a href="https://github.com/ramazancetinkaya/mysql-backup/blob/main/MESSAGE.md">1 new message</a> from the developer.
</p>

## 🌟 Star this Repository!

If you find the MySQL Backup & Restore library helpful or interesting, consider giving it a star! ⭐️

Your star helps us grow and motivates us to continue improving the library. It also makes it easier for others to discover and benefit from this project.

### How to Star?

1. **Login to Your GitHub Account:** You need to have a GitHub account.
2. **Visit the Repository:** Go to the [MySQL Backup & Restore Repository](https://github.com/ramazancetinkaya/mysql-backup).
3. **Click the Star Button:** On the top-right corner of the page, you'll find a "Star" button. Click on it!

That's it! Thank you for your support! 🚀

## Table of Contents

* [Introduction](#introduction)
* [About the Project](#about-the-project)
* [Screenshot](#screenshot)
* [Features](#features)
* [Requirements](#requirements)
* [Installation](#installation)
* [Usage](#usage)
* [Disclaimer](#disclaimer)
* [Contributing](#contributing)
* [Contact](#contact)
* [Credits](#credits)
* [License](#license)
* [Copyright](#copyright)

## Introduction

This library is designed to be easy to use for both beginners and experienced developers.

## About the Project

The MySQL Backup & Restore Library is a PHP library that provides functionality for backing up and restoring MySQL databases. It offers a simple and intuitive API, leveraging the power of PDO for seamless database operations.

### Screenshot

![Screenshot](https://i.imgur.com/piAh4Xf.png)

## Features

* Back up a MySQL database to a SQL file.
* Restore a MySQL database from a SQL backup file.
* Automatic generation of backup filenames with date and time.
* Include informative header comments in the backup file.
* Support for modern PHP versions. (PHP 8 and above)

## Requirements

- PHP version 8.0 or higher
- PDO extension enabled
- MySQL database
- Composer (for installation)

## Installation

This library can be easily installed using [Composer](https://getcomposer.org/), a modern PHP dependency manager.

### Step 1: Install Composer

If you don't have Composer installed, you can download and install it by following the instructions on the [official Composer website](https://getcomposer.org/download/).

### Step 2: Install the Library

Once Composer is installed, you can install the `mysql-backup` library by running the following command in your project's root directory:

```bash
composer require ramazancetinkaya/mysql-backup
```

## Usage

```php
require_once 'vendor/autoload.php'; // Include Composer's autoloader

use ramazancetinkaya\BackupLibrary;

// Set up your database connection
$dsn = 'mysql:host=localhost;dbname=your_database';
$username = 'your_username';
$password = 'your_password';

$pdo = new PDO($dsn, $username, $password);

// Create an instance of BackupLibrary
$backupLibrary = new BackupLibrary($pdo);
```

- Perform a database backup:
```php
// Perform a backup
$backupPath = 'backup/';
$backupSuccessful = $backupLibrary->backupDatabase($backupPath);

if ($backupSuccessful) {
    echo "Database backup created successfully.";
} else {
    echo "Database backup failed!";
}

```

- Perform a database restore:
```php
// Restore a database
$backupFile = 'backup/backup_20230622_134302.sql';
$restoreSuccessful = $backupLibrary->restoreDatabase($backupFile);

if ($restoreSuccessful) {
    echo "Database restored successfully.";
} else {
    echo "Database restoration failed!";
}
```

## Disclaimer

This library is provided as-is without any warranties, expressed or implied. The use of this library is at your own risk, and the developers will not be liable for any damages or losses resulting from its use.

While every effort has been made to ensure the accuracy and reliability of the code in this library, it's important to understand that no guarantee is provided regarding its correctness or suitability for any purpose.

Users are encouraged to review and test the functionality of this library in their own environments before deploying it in production or critical systems.

This disclaimer extends to all parts of the library and its documentation.

**By using the Library, you agree to these terms and conditions. If you do not agree with any part of this disclaimer, do not use the Library.**

---

This disclaimer was last updated on January 4, 2024.

## Contributing

Contributions are welcome! If you encounter any issues or have suggestions for improvements, please create an issue or submit a pull request. 
Make sure to follow the existing coding style and provide tests for your changes.

## Contact

For any inquiries or feedback, feel free to reach out to us via email.

📧 Email: [ramazancetinkayadev@outlook.com](mailto:ramazancetinkayadev@outlook.com)

## Credits

This library was made possible by the following awesome contributors:

- **Ramazan Çetinkaya** - [@ramazancetinkaya](https://github.com/ramazancetinkaya)
  - Lead Developer

Special thanks to the following resources:

- [PHP Documentation](https://www.php.net/docs.php) - Valuable information on PHP programming language.
- [Composer](https://getcomposer.org/) - Dependency manager for PHP.

If you've contributed to this project and your name is not listed, please let us know, and we'll add you!

Thank you to everyone who has helped make this project better!

## License

This project is licensed under the MIT License. For more details, see the [LICENSE](LICENSE) file.

## Copyright

© 2024 Ramazan Çetinkaya. All rights reserved.
