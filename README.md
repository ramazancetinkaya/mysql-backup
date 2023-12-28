<h1 align="center">MySQL Backup & Restore Library</h1>

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Version](https://img.shields.io/badge/version-1.0.0-green.svg)](https://github.com/ramazancetinkaya/password-generator)
[![GitHub stars](https://img.shields.io/github/stars/ramazancetinkaya/mysql-backup.svg?style=social)](https://github.com/ramazancetinkaya/mysql-backup/stargazers)

<p align="center">
🌟🌟🌟 Star the repository if you find it useful! 🌟🌟🌟
</p>

<p align="center">
  <a href="https://github.com/ramazancetinkaya/mysql-backup">
    <img src="https://www.mysql.com/common/logos/logo-mysql-170x115.png" alt="Logo">
  </a>

  <h3 align="center">MySQL Backup & Restore Library</h3>

  <p align="center">
    A powerful and modern PHP library for backing up and restoring MySQL databases using PDO.
    <br />
    <a href="https://github.com/ramazancetinkaya/mysql-backup/blob/main/README.md"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://github.com/ramazancetinkaya/mysql-backup/issues">Report a Bug</a>
    ·
    <a href="https://github.com/ramazancetinkaya/mysql-backup/pulls">New Pull Request</a>
  </p>
</p>

## Table of Contents

* [Introduction](#introduction)
* [About the Project](#about-the-project)
* [Screenshot](#screenshot)
* [Features](#features)
* [Requirements](#requirements)
* [Getting Started](#getting-started)
* [Installation](#installation)
* [Usage](#usage)
* [Disclaimer](#disclaimer)
* [Contributing](#contributing)
* [License](#license)
* [Authors](#authors)
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

- PHP 8 or higher
- PDO extension enabled
- MySQL database

## Getting Started

To get started with the MySQL Backup & Restore Library, follow these steps:

### Installation

1) Composer

- You can install the Logger library via Composer. Run the following command:

```bash
composer require ramazancetinkaya/mysql-backup
```

Make sure you have Composer installed on your system. If not, you can download and install it from the official <a href="https://getcomposer.org/">Composer</a> website.

2) Clone the repository

- To clone this repository to your local machine, use the following command:

```sh
git clone https://github.com/ramazancetinkaya/mysql-backup.git
```

Make sure you have <a href="https://git-scm.com/">Git</a> installed on your system before running this command.

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

- To back up a MySQL database to an SQL file:
```php
// Perform a backup
$backupPath = 'backup/';
$backupSuccessful = $backupLibrary->backupDatabase($backupPath);

if ($backupSuccessful) {
    echo "Backup successful!";
} else {
    echo "Backup failed!";
}

```

- To restore a MySQL database from a SQL backup file:
```php
// Restore a database
$backupFile = 'backup/backup_20230622_134302.sql';
$restoreSuccessful = $backupLibrary->restoreDatabase($backupFile);

if ($restoreSuccessful) {
    echo "Database restored successfully!";
} else {
    echo "Database restoration failed!";
}
```

## Disclaimer

The code and information provided in this repository are for educational and informational purposes only. The author and contributors make no representations as to the accuracy, completeness, currentness, suitability, or validity of any information in this repository and will not be liable for any errors, omissions, or delays in this information or any losses, injuries, or damages arising from its use.

The use of the code and information in this repository is at your own risk. It is your responsibility to ensure that any code or information you use from this repository is free of viruses or other harmful components. The author and contributors disclaim any responsibility for any harm resulting from the use of this repository.

Please note that the code and information provided in this repository may be subject to change without notice. The author and contributors reserve the right to modify, update, or remove any content in this repository at their discretion.

While efforts are made to keep the information up to date and accurate, it is recommended to refer to official documentation or seek professional advice when using the code or information in this repository for any specific purpose.

By using the code and information in this repository, you acknowledge and agree to these terms and conditions outlined in this disclaimer.

## Contributing

Contributions are welcome! If you encounter any issues or have suggestions for improvements, please create an issue or submit a pull request. 
Make sure to follow the existing coding style and provide tests for your changes.

## License

This project is licensed under the MIT License. See the LICENSE file for details.

## Authors

**Ramazan Çetinkaya**
- <https://github.com/ramazancetinkaya>

## Copyright

Copyright (c) [2023] [Ramazan Çetinkaya]
