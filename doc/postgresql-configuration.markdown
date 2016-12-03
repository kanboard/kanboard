Postgresql configuration
========================

By default, Kanboard use Sqlite to store its data but it's also possible to use Postgresql.

Requirements
------------

- Postgresql >= 9.3
- The PHP extension `pdo_pgsql` installed (Debian/Ubuntu: `apt-get install php5-pgsql`)

Configuration
-------------

### Create an empty database with the command `pgsql`:

```sql
CREATE DATABASE kanboard;
```

### Create a config file

The file `config.php` should contain those values:

```php
<?php

// We choose to use Postgresql instead of Sqlite
define('DB_DRIVER', 'postgres');

// Mysql parameters
define('DB_USERNAME', 'REPLACE_ME');
define('DB_PASSWORD', 'REPLACE_ME');
define('DB_HOSTNAME', 'REPLACE_ME');
define('DB_NAME', 'kanboard');
```

Note: You can also rename the template file `config.default.php` to `config.php`.

### Importing SQL dump (alternative method)

For the first time, Kanboard will run one by one each database migration and this process can take some time according to your configuration.

To avoid any issues or potential timeouts, you can initialize the database directly by importing the SQL schema:

```bash
psql -U postgres my_database < app/Schema/Sql/postgres.sql
```

The file `app/Schema/Sql/postgres.sql` is a SQL dump that represents the last version of the database.
