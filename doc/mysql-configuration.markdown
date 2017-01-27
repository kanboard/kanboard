MySQL/MariaDB Configuration
===========================

By default Kanboard use Sqlite to stores its data.
However it's possible to use MySQL or MariaDB instead of Sqlite.

Requirements
------------

- MySQL server
- The PHP extension `pdo_mysql` installed

Note: Kanboard is tested with **MySQL >= 5.5 and MariaDB >= 10.0**

MySQL configuration
-------------------

### Create a database

The first step is to create a database on your MySQL server.
By example, you can do that with the command line mysql client:

```sql
CREATE DATABASE kanboard;
```

### Create a config file

The file `config.php` should contains those values:

```php
<?php

// We choose to use MySQL instead of Sqlite
define('DB_DRIVER', 'mysql');

// MySQL parameters
define('DB_USERNAME', 'REPLACE_ME');
define('DB_PASSWORD', 'REPLACE_ME');
define('DB_HOSTNAME', 'REPLACE_ME');
define('DB_NAME', 'kanboard');
```

Note: You can also rename the template file `config.default.php` to `config.php`.

### Importing SQL dump (alternative method)

For the first time, Kanboard will run one by one each database migration and this process can take some time according to your configuration.

To avoid any potential timeout you can initialize the database directly by importing the SQL schema:

```bash
mysql -u root -p my_database < app/Schema/Sql/mysql.sql
```

The file `app/Schema/Sql/mysql.sql` is a SQL dump that represents the last version of the database.

SSL configuration
-----------------

These parameters have to be defined to enable the MySQL SSL connection:

```php
// MySQL SSL key
define('DB_SSL_KEY', '/path/to/client-key.pem');

// MySQL SSL certificate
define('DB_SSL_CERT', '/path/to/client-cert.pem');

// MySQL SSL CA
define('DB_SSL_CA', '/path/to/ca-cert.pem');
```
