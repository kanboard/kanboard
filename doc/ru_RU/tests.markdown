Автоматизированные тесты[¶](#automated-tests "Ссылка на этот заголовок")

========================================================================



[PHPUnit](https://phpunit.de/) используется для запуска автоматизированных тестов в Канборд.



Вы можете запускать тесты для разных баз данных (Sqlite, Mysql and Postgresql), чтобы убедится, что результаты будут одинаковые.



Требования[¶](#requirements "Ссылка на этот заголовок")

-------------------------------------------------------



-   Компьютер Linux/Unix



-   PHP cli



-   Установленный PHPUnit



-   Mysql и Postgresql (опционально)



Unit тесты[¶](#unit-tests "Ссылка на этот заголовок")

-----------------------------------------------------



### Тестирование с Sqlite[¶](#test-with-sqlite "Ссылка на этот заголовок")



Sqlite тестирование использует базу данных в памяти, без использования записи на файловую систему.



Конфигурационный файл PHPUnit - `tests/units.sqlite.xml`{.docutils .literal}. Из директории Kanboard запустите команду `phpunit -c tests/units.sqlite.xml`{.docutils .literal}.



Пример:



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



### Тестирование с Mysql[¶](#test-with-mysql "Ссылка на этот заголовок")



У вас должна быть локально установлена база данных Mysql или MariaDb.



По умолчанию, используются следующие учетные данные:



-   Hostname: **localhost**

-   Username: **root**

-   Password: none

-   Database: **kanboard\_unit\_test**



При каждом выполнении база данных удаляется и создается снова.



Конфигурационный файл HPUnit - `tests/units.mysql.xml`{.docutils .literal}. Из директории Kanboard запустите команду `phpunit -c tests/units.mysql.xml`{.docutils .literal}.



### Тестирование с Postgresql[¶](#test-with-postgresql "Ссылка на этот заголовок")



У вас должен быть локально установлен Postgresql.



По умолчанию, используются следующие учетные данные:



-   Hostname: **localhost**

-   Username: **postgres**

-   Password: none

-   Database: **kanboard\_unit\_test**



Убедитесь, что пользователь `postgres`{.docutils .literal} может создавать и удалять базу данных. База данных пересоздается при каждом выполнении теста.



Конфигурационных файл PHPUnit - `tests/units.postgres.xml`{.docutils .literal}. Из директории Kanboard, запустите команду `phpunit -c tests/units.postgres.xml`{.docutils .literal}.



Тесты интеграции[¶](#integration-tests "Ссылка на этот заголовок")

------------------------------------------------------------------



Фактически тестируются только вызовы API.



Реальные HTTP calls выполняются с этими тестами. Поэтому, необходим локальный экземпляр Канборда, который слушает на `http://localhost:8000/`{.docutils .literal}.



Все данные будут удалены/изменены при тестировании. Более того скрипт будет сброшен и установлен новый ключ API.



1.  Запустите локольный экземпляр Канборда: `php -S 127.0.0.1:8000`{.docutils .literal}



2.  Запустите тест в другом терминале



Этот же метод используется для запуска тестов для разных баз данных:



-   Sqlite: `phpunit -c tests/integration.sqlite.xml`{.docutils .literal}

-   Mysql: `phpunit -c tests/integration.mysql.xml`{.docutils .literal}

-   Postgresql: `phpunit -c tests/integration.postgres.xml`{.docutils .literal}



Пример:



    phpunit -c tests/integration.sqlite.xml



    PHPUnit 5.0.0 by Sebastian Bergmann and contributors.



    ...............................................................  63 / 135 ( 46%)

    ............................................................... 126 / 135 ( 93%)

    .........                                                       135 / 135 (100%)



    Time: 1.18 minutes, Memory: 14.75Mb



    OK (135 tests, 526 assertions)



Непрерывная интеграция с Travis-CI[¶](#continuous-integration-with-travis-ci "Ссылка на этот заголовок")

--------------------------------------------------------------------------------------------------------



После каждого commit влитого в мой репозиторий, юнит тесты выполняются для 5 различных версий PHP:



-   PHP 7.0

-   PHP 5.6

-   PHP 5.5

-   PHP 5.4

-   PHP 5.3



При тестировании каждой версии PHP используются 3 поддерживаемые базы данных: Sqlite, Mysql and Postgresql.



Конфигурационный файл Travis - `.travis.yml`{.docutils .literal} - находится в корневой директории Kanboard.



### [Оглавление](index.markdown)



-   [Автоматизированные тесты](#)

    -   [Требования](#requirements)

    -   [Unit тесты](#unit-tests)

        -   [Тестирование с Sqlite](#test-with-sqlite)

        -   [Тестирование с Mysql](#test-with-mysql)

        -   [Тестирование с Postgresql](#test-with-postgresql)

    -   [Тесты интеграции](#integration-tests)

    -   [Непрерывная интеграция с Travis-CI](#continuous-integration-with-travis-ci)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/tests.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/tests.txt)

