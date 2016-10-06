Testing automatico
===============

[PHPUnit](https://phpunit.de/) es usado para correr pruebas automaticas en Kanboard.

Tu puedes correr pruebas entre diferentes base de datos (Sqlite, Mysql and Postgresql) para asegurarse de que el resultado es el mismo en todas partes

Requirementos
------------

- Maquina Linux/Unix 
- PHP
- PHPUnit instalado
- Mysql y Postgresql (opcional)
- Selenium (opcional)
- Firefox (opcional)

Pruebas unitarias
-----------------

### Prueba con Sqlite

Las pruebas de sqlite utilizan una base de datos en memoria, no hay nada escrito sobre el sistema de archivos. 

El archivo es PHPUnit `tests/units.sqlite.xml`.
Desde el directorio Kanboard, ejecutar el comando `phpunit -c tests/units.sqlite.xml`.

Ejemplo:

```bash
phpunit -c tests/units.sqlite.xml

PHPUnit 5.0.0 por Sebastian Bergmann y contribuidores.

...............................................................  63 / 649 (  9%)
............................................................... 126 / 649 ( 19%)
............................................................... 189 / 649 ( 29%)
............................................................... 252 / 649 ( 38%)
............................................................... 315 / 649 ( 48%)
............................................................... 378 / 649 ( 58%)
............................................................... 441 / 649 ( 67%)
............................................................... 504 / 649 ( 77%)
............................................................... 567 / 649 ( 87%)
............................................................... 630 / 649 ( 97%)
...................                                             649 / 649 (100%)

Time: 1.22 minutes, Memory: 151.25Mb

OK (649 tests, 43595 assertions)
```

### Pruebas con Mysql

Solamente debes tener instaldo Mysql o MariaDb en localhost

Por default, se utilizan estas credenciales:

- Hostname: **localhost**
- Username: **root**
- Password: none
- Database: **kanboard_unit_test**

Para cada ejecución la base de datos se elimina y crea de nuevo.

El archivo PHPUnit es `tests/units.mysql.xml`.
Desde el directorio Kanboard, ejecutar el comando `phpunit -c tests/units.mysql.xml`.

### Pruebas con Postgresql

Solamente debes tener instaldo Postgresql en tu localhost.

Por default, se utilizan estas credenciales:

- Hostname: **localhost**
- Username: **postgres**
- Password: none
- Database: **kanboard_unit_test**

Asegúrese de que el usuario `postgres` pueda crear y eliminar bases de datos.
La base de datos se vuelve a crear para cada ejecución.

El archivo PHPUnit es  `tests/units.postgres.xml`.
Desde el directorio Kanboard, ejecutar el comando  `phpunit -c tests/units.postgres.xml`.

Las pruebas de integración
-----------------

Las pruebas de integración se utilizan principalmente para probar la API.
Las series de ensayos están haciendo peticiones HTTP reales a la aplicación que se ejecuta dentro de un contenedor.

### Requerimientos

- PHP
- Composer
- Unix operating system (Mac OS or Linux)
- Docker
- Docker Compose

### Ejecución de pruebas de integración

Pruebas de integración están utilizando contenedores Docker.
Hay 3 ambiente diferente disponible para ejecutar las pruebas contra cada base de datos compatible.

Puede utilizar estos comandos para ejecutar cada conjunto de pruebas:

```bash
# Run tests with Sqlite
make integration-test-sqlite

# Run tests with Mysql
make integration-test-mysql

# Run tests with Postgres
make integration-test-postgres
```

Prueba de aceptacion
----------------

Las pruebas de aceptación (a veces también conocidas como end-to-end tests, y pruebas funcionales) ponen a prueba la funcionalidad real de la interfaz de usuario en un navegador usando Selenium.

Con el fin de ejecutar estas pruebas debe tener [Selenium Standalone Server](http://www.seleniumhq.org/download/) instalado, y una versión compatible de Firefox..

El archivo de configuración PHPUnit es `tests/acceptance.xml`.
Con Selenium y la aplicación en ejecución Kanboard, desde su directorio Kanboard, ejecute el comando `make test-browser`. Esto iniciará la suite de prueba y verá Firefox se abre automáticamente y realizar las acciones especificadas en las pruebas de aceptación
Ejemplo:

```bash
$ make test-browser
PHPUnit 4.8.26 by Sebastian Bergmann and contributors.

..

Time: 5.59 seconds, Memory: 5.25MB

OK (2 tests, 5 assertions)
```


Continuar la integración con Travis-CI
-------------------------------------

Después de cada commint pushed en el repositorio principal, las pruebas unitarias se ejecutan a través de 5 diferentes versiones de PHP:

- PHP 7.0
- PHP 5.6
- PHP 5.5
- PHP 5.4
- PHP 5.3

Cada versión de php esta probada contra las 3 bases de datos soportadas: Sqlite, Mysql and Postgresql.

el archivo de configuración Travis `.travis.yml` esta locilzado en el directorio root de Kanboard.
