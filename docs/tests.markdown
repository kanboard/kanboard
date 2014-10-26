How to run units and functionals tests?
=======================================

[PHPUnit](https://phpunit.de/) is used to run automatic tests on Kanboard.

You can run tests across different databases (Sqlite, Mysql and Postgresql) to be sure that the result is the same everywhere.

Requirements
------------

- Linux/Unix machine
- PHP command line
- PHPUnit installed
- Mysql and Postgresql (optional)

Install the latest version of PHPUnit
-------------------------------------

Simply download the PHPUnit PHAR et copy the file somewhere in your `$PATH`:

```bash
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
phpunit --version
PHPUnit 4.2.6 by Sebastian Bergmann.
```

Running unit tests
------------------

### Testing with Sqlite

Sqlite tests use a in-memory database, nothing is written on the filesystem.

The config file is `tests/units.sqlite.xml`.
From your Kanboard directory, run the command `phpunit -c tests/units.sqlite.xml`.

Example:

```bash
phpunit -c tests/units.sqlite.xml

PHPUnit 4.2.6 by Sebastian Bergmann.

Configuration read from /Volumes/Devel/apps/kanboard/tests/units.sqlite.xml

................................................................. 65 / 74 ( 87%)
.........

Time: 9.05 seconds, Memory: 17.75Mb

OK (74 tests, 6145 assertions)
```

### Testing with Mysql

You must have Mysql or MariaDb installed on localhost.

By default, those credentials are used:

- Hostname: **localhost**
- Username: **root**
- Password: nothing (blank)
- Database: **kanboard_unit_test**

For each execution the database is dropped and created again.

The config file is `tests/units.mysql.xml`.
From your Kanboard directory, run the command `phpunit -c tests/units.mysql.xml`.

Example:

```bash
phpunit -c tests/units.mysql.xml

PHPUnit 4.2.6 by Sebastian Bergmann.

Configuration read from /Volumes/Devel/apps/kanboard/tests/units.mysql.xml

................................................................. 65 / 74 ( 87%)
.........

Time: 49.77 seconds, Memory: 17.50Mb

OK (74 tests, 6145 assertions)
```

### Testing with Postgresql

You must have Postgresql installed on localhost.

By default, those credentials are used:

- Hostname: **localhost**
- Username: **postgres**
- Password: **postgres**
- Database: **kanboard_unit_test**

Be sure to allow the user `postgres` to create and drop databases.
For each execution the database is dropped and created again.

The config file is `tests/units.postgres.xml`.
From your Kanboard directory, run the command `phpunit -c tests/units.postgres.xml`.

Example:

```bash
phpunit -c tests/units.postgres.xml

PHPUnit 4.2.6 by Sebastian Bergmann.

Configuration read from /Volumes/Devel/apps/kanboard/tests/units.postgres.xml

................................................................. 65 / 74 ( 87%)
.........

Time: 52.66 seconds, Memory: 17.50Mb

OK (74 tests, 6145 assertions)
```

Running functionals tests
-------------------------

Actually only the API calls are tested.

Real HTTP calls are made with those tests.
So a local instance of Kanboard is necessary and must listen on `http://localhost:8000`.

Don't forget that all data will be removed/altered by the test suite.
Moreover the script will reset and set a new API key.

1. Start a local instance of Kanboard `php -S 127.0.0.1:8000`
2. Run the test suite from another terminal

The same method as above is used to run tests across different databases:

- Sqlite: `phpunit -c tests/functionals.sqlite.xml`
- Mysql: `phpunit -c tests/functionals.mysql.xml`
- Postgresql: `phpunit -c tests/functionals.postgres.xml`

Example:

```bash
phpunit -c tests/functionals.sqlite.xml

PHPUnit 4.2.6 by Sebastian Bergmann.

Configuration read from /Volumes/Devel/apps/kanboard/tests/functionals.sqlite.xml

..........................................

Time: 1.72 seconds, Memory: 4.25Mb

OK (42 tests, 160 assertions)
```

Continuous Integration with Travis
----------------------------------

After each commit pushed on the main repository, unit tests are executed across 4 different major versions of PHP.

The Travis config file `.travis.yml` is located on the root directory of Kanboard:

```yaml
language: php

php:
  - "5.6"
  - "5.5"
  - "5.4"
  - "5.3"

before_script: wget https://phar.phpunit.de/phpunit.phar
script: php phpunit.phar -c tests/units.sqlite.xml
```

As you can see, tests are executed with PHP 5.3, 5.4, 5.5 and 5.6.
However, only Sqlite unit tests are executed on Travis.
