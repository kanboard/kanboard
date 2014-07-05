Postgresql configuration
========================

By default, Kanboard use Sqlite to store its data but it's also possible to use Postgresql.

Requirements
------------

- A Postgresql server already installed and configured
- The PHP extension `pdo_pgsql` installed (Debian/Ubuntu: `apt-get install php5-pgsql`)

Configuration
-------------

### Create an empty database with the command `pgsql`:

```sql
CREATE DATABASE kanboard;
```

### Create a config file

Inside our config file write those lines:

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

Now, you are ready to use Postgresql.
