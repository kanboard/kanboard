Plugin Schema Migrations
========================

Kanboard executes database migrations automatically for you.
Migrations must be stored in a folder **Schema** and the filename must be the same as the database driver:

```bash
Schema
├── Mysql.php
├── Postgres.php
└── Sqlite.php
```

Each file contains all migrations, here an example for Sqlite:

```php
<?php

namespace Kanboard\Plugin\Something\Schema;

const VERSION = 1;

function version_1($pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS something (
        "id" INTEGER PRIMARY KEY,
        "project_id" INTEGER NOT NULL,
        "something" TEXT,
        FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
    )');
}
```

- The constant `VERSION` is the last version of your schema
- Each function is a migration `version_1()`, `version_2()`, etc.
- A `PDO` instance is passed as first argument
- Everything is executed inside a transaction, if something doesn't work a rollback is performed and the error is displayed to the user

Kanboard will compare the version defined in your schema and the version stored in the database. If the versions are different, Kanboard will execute one by one each migration until to reach the last version.
