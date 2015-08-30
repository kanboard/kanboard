How to use Mysql or MariaDB instead of Sqlite
=============================================

By default Kanboard use Sqlite to stores its data.
However it's possible to use Mysql or MariaDB instead of Sqlite.

Requirements
------------

- Mysql server
- The PHP extension `pdo_mysql` installed (Debian/Ubuntu: `apt-get install php5-mysql`)

Note: Kanboard is tested with **Mysql >= 5.5 and MariaDB >= 10.0**

Mysql configuration
-------------------

### Create a database

The first step is to create a database on your Mysql server.
By example, you can do that with the command line mysql client:

```sql
CREATE DATABASE kanboard;
```

### Create a config file

The file `config.php` should contains those values:

```php
<?php

// We choose to use Mysql instead of Sqlite
define('DB_DRIVER', 'mysql');

// Mysql parameters
define('DB_USERNAME', 'REPLACE_ME');
define('DB_PASSWORD', 'REPLACE_ME');
define('DB_HOSTNAME', 'REPLACE_ME');
define('DB_NAME', 'kanboard');
```

Note: You can also rename the template file `config.default.php` to `config.php`.
