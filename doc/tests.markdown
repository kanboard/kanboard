Automated tests
===============

[PHPUnit](https://phpunit.de/) is used to run automated tests on Kanboard.

You can run tests across different databases (Sqlite, Mysql and Postgresql) to be sure that the result is the same everywhere.

Requirements
------------

- Linux/Unix machine
- PHP
- PHPUnit installed
- Mysql and Postgresql (optional)
- Selenium (optional)
- Firefox (optional)

Unit Tests
----------

### Test with Sqlite

Sqlite tests use a in-memory database, nothing is written on the file system.

The PHPUnit config file is `tests/units.sqlite.xml`.
From your Kanboard directory, run the command `phpunit -c tests/units.sqlite.xml`.

Example:

```bash
phpunit -c tests/units.sqlite.xml

PHPUnit 5.0.0 by Sebastian Bergmann and contributors.

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

### Test with Mysql

You must have Mysql or MariaDb installed on localhost.

By default, those credentials are used:

- Hostname: **localhost**
- Username: **root**
- Password: none
- Database: **kanboard_unit_test**

For each execution the database is dropped and created again.

The PHPUnit config file is `tests/units.mysql.xml`.
From your Kanboard directory, run the command `phpunit -c tests/units.mysql.xml`.

### Test with Postgresql

You must have Postgresql installed on localhost.

By default, those credentials are used:

- Hostname: **localhost**
- Username: **postgres**
- Password: none
- Database: **kanboard_unit_test**

Be sure to allow the user `postgres` to create and drop databases.
The database is recreated for each execution.

The PHPUnit config file is `tests/units.postgres.xml`.
From your Kanboard directory, run the command `phpunit -c tests/units.postgres.xml`.

Integration Tests
-----------------

Integration tests are mainly used to test the API. 
The test suites are making real HTTP calls to the application that run inside a container.

### Requirements

- PHP
- Composer
- Unix operating system (Mac OS or Linux)
- Docker
- Docker Compose

### Running integration tests

Integration tests are using Docker containers. 
There are 3 different environment available to run tests against each supported database.

You can use these commands to run each test suite:

```bash
# Run tests with Sqlite
make integration-test-sqlite

# Run tests with Mysql
make integration-test-mysql

# Run tests with Postgres
make integration-test-postgres
```

Acceptance Tests
----------------

Acceptance tests (also sometimes known as end-to-end tests, and functional tests) test the actual functionality of the UI in a browser using Selenium.

In order to run these tests you must have [Selenium Standalone Server](http://www.seleniumhq.org/download/) installed, and a compatible version of Firefox.

The PHPUnit config file is `tests/acceptance.xml`.
With Selenium and the Kanboard app running, from your Kanboard directory, run the command `make test-browser`. This will initiate the testing suite and you will see Firefox open automatically and perform the actions specified in the acceptance tests.  

Example:

```bash
$ make test-browser
PHPUnit 4.8.26 by Sebastian Bergmann and contributors.

..

Time: 5.59 seconds, Memory: 5.25MB

OK (2 tests, 5 assertions)
```


Continuous Integration with Travis-CI
-------------------------------------

After each commit pushed on the main repository, unit tests are executed across 5 different versions of PHP:

- PHP 7.0
- PHP 5.6
- PHP 5.5
- PHP 5.4
- PHP 5.3

Each version of PHP is tested against the 3 supported database: Sqlite, Mysql and Postgresql.

The Travis config file `.travis.yml` is located on the root directory of Kanboard.
