Plugin Migración de esquemas
============================

Kanboard realiza migraciones de base de datos automaticamente para ti.
Las migraciones deben ser almacenadas en una carpeta **Schema** y el nombre de archivo debe ser el mismo que el controlador de base de datos:

```bash
Schema
├── Mysql.php
├── Postgres.php
└── Sqlite.php
```

Cada archivo contiene todas las migraciones, aqui un ejemple para Sqlite:

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

- La constante `VERSION` es la ultima versión de tu esquema
- Cada funcion es una migración `version_1()`, `version_2()`, etc.
- Una instancia `PDO` es pasado como un primer argumneto
- Todo se ejecuta dentro de una transacción , si algo no funciona se realiza una operación de rollback y se muestra el error al usuario

Kanboard siempre compara la version definida en tu esquema y la version almacenada en la base de datos. Si la versiones son diferentes, kanboard siempre ejecuta cada migración una por una hasta alcanzar la ultima version.

