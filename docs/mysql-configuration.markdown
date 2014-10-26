How to use Mysql or MariaDB instead of Sqlite
=============================================

By default Kanboard use Sqlite to stores its data.
However it's possible to use Mysql or MariaDB instead of Sqlite.

Mysql configuration
-------------------

### Create a database

The first step is to create a database on your Mysql server.
By example, you can do that with the command line mysql client:

```sql
CREATE DATABASE kanboard;
```

### Create a config file

All application constants can be overrided by using a config file at the root of the project.
The second step is to create a config file named `config.php`:

```
.
├── LICENSE
├── common.php
├── ....
├── config.php <== Our config file
├── ....
├── index.php
├── ....
└── vendor
```

### Define Mysql parameters

Copy and paste this config file and replace the values:

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
