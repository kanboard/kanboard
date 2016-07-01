Automated tests
===============

`PHPUnit <https://phpunit.de/>`__ is used to run automated tests on
Kanboard.

You can run tests across different databases (Sqlite, Mysql and
Postgresql) to be sure that the result is the same everywhere.

Requirements
------------

-  Linux/Unix machine
-  PHP cli
-  PHPUnit installed
-  Mysql and Postgresql (optional)

Unit Tests
----------

Test with Sqlite
~~~~~~~~~~~~~~~~

Sqlite tests use a in-memory database, nothing is written on the file
system.

The PHPUnit config file is ``tests/units.sqlite.xml``. From your
Kanboard directory, run the command
``phpunit -c tests/units.sqlite.xml``.

Example:

.. code:: bash

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

Test with Mysql
~~~~~~~~~~~~~~~

You must have Mysql or MariaDb installed on localhost.

By default, those credentials are used:

-  Hostname: **localhost**
-  Username: **root**
-  Password: none
-  Database: **kanboard\_unit\_test**

For each execution the database is dropped and created again.

The PHPUnit config file is ``tests/units.mysql.xml``. From your Kanboard
directory, run the command ``phpunit -c tests/units.mysql.xml``.

Test with Postgresql
~~~~~~~~~~~~~~~~~~~~

You must have Postgresql installed on localhost.

By default, those credentials are used:

-  Hostname: **localhost**
-  Username: **postgres**
-  Password: none
-  Database: **kanboard\_unit\_test**

Be sure to allow the user ``postgres`` to create and drop databases. The
database is recreated for each execution.

The PHPUnit config file is ``tests/units.postgres.xml``. From your
Kanboard directory, run the command
``phpunit -c tests/units.postgres.xml``.

Integration Tests
-----------------

Actually only the API calls are tested.

Real HTTP calls are made with those tests. So a local instance of
Kanboard is necessary and must listen on ``http://localhost:8000/``.

All data will be removed/altered by the test suite. Moreover the script
will reset and set a new API key.

1. Start a local instance of Kanboard ``php -S 127.0.0.1:8000``
2. Run the test suite from another terminal

The same method as above is used to run tests across different
databases:

-  Sqlite: ``phpunit -c tests/integration.sqlite.xml``
-  Mysql: ``phpunit -c tests/integration.mysql.xml``
-  Postgresql: ``phpunit -c tests/integration.postgres.xml``

Example:

.. code:: bash

    phpunit -c tests/integration.sqlite.xml

    PHPUnit 5.0.0 by Sebastian Bergmann and contributors.

    ...............................................................  63 / 135 ( 46%)
    ............................................................... 126 / 135 ( 93%)
    .........                                                       135 / 135 (100%)

    Time: 1.18 minutes, Memory: 14.75Mb

    OK (135 tests, 526 assertions)

Continuous Integration with Travis-CI
-------------------------------------

After each commit pushed on the main repository, unit tests are executed
across 5 different versions of PHP:

-  PHP 7.0
-  PHP 5.6
-  PHP 5.5
-  PHP 5.4
-  PHP 5.3

Each version of PHP is tested against the 3 supported database: Sqlite,
Mysql and Postgresql.

The Travis config file ``.travis.yml`` is located on the root directory
of Kanboard.
