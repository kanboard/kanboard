Configuración Mysql/MariaDB 
===========================


Por defecto Kanboard utilizar SQLite para almacenar tus datos.
Sin embargo, es posible usar MySQL o MariaDB en lugar de SQLite.

Requirimientos
------------

- Mysql server
- Instalar la extensión `pdo_mysql` en PHP

Nota: Kanboard esta testeada con  **Mysql >= 5.5 y MariaDB >= 10.0**

configuración Mysql 
-------------------

### Crear una base de datos

El primer paso es crear una base de datos en tu servidor MySQL
Por ejemplo, se puede hacer eso con el cliente de línea de comandos mysql:

```sql
CREATE DATABASE kanboard;
```

### Crear un archivo de configuración

El archivo `config.php` debería contener estos valores:

```php
<?php

// Elegimos el uso de MySQL en lugar de SQLite
define('DB_DRIVER', 'mysql');

// Mysql parametros
define('DB_USERNAME', 'REPLACE_ME');
define('DB_PASSWORD', 'REPLACE_ME');
define('DB_HOSTNAME', 'REPLACE_ME');
define('DB_NAME', 'kanboard');
```

Nota: Se puede renombrar el archivo de plantilla `config.default.php` a `config.php`.

### Importando SQL dump (metodo alternativo)


Por primera vez, se ejecutará Kanboard uno por uno cada migración de base de datos y este proceso puede tardar algún tiempo de acuerdo a su configuración.

Para evitar cualquier tiempo de espera de potencial se puede inicializar la base de datos directamente importando el esquema de SQL:

```bash
mysql -u root -p my_database < app/Schema/Sql/mysql.sql
```

El archivo `app/Schema/Sql/mysql.sql` es un dump SQL  que representa la ultima versión de la base de datos

SSL configuración
-----------------

Estos parámetros tienen que ser definidas para permitir la conexión SSL Mysql:

```php
// Mysql SSL key
define('DB_SSL_KEY', '/path/to/client-key.pem');

// Mysql SSL certificados
define('DB_SSL_CERT', '/path/to/client-cert.pem');

// Mysql SSL CA
define('DB_SSL_CA', '/path/to/ca-cert.pem');
```
