Настройка Postgresql[¶](#postgresql-configuration "Ссылка на этот заголовок")

=============================================================================



По умолчанию, Канборд использует для хранения данных Sqlite, но возможно использовать и Postgresql.



Требования[¶](#requirements "Ссылка на этот заголовок")

-------------------------------------------------------



-   Установленный и настроенный сервер Postgresql



-   Установленное PHP расширение - `pdo_pgsql`{.docutils .literal} (Debian/Ubuntu: `apt-get install php5-pgsql`{.docutils .literal})



Примечание: работа Канборда протестирована с **Postgresql 9.3 и 9.4**



Настройка[¶](#configuration "Ссылка на этот заголовок")

-------------------------------------------------------



### Создайте пустую базу данных выполнив команду `pgsql`{.docutils .literal}:[¶](#create-an-empty-database-with-the-command-pgsql "Ссылка на этот заголовок")



    CREATE DATABASE kanboard;



### Создание конфигурационного файла[¶](#create-a-config-file "Ссылка на этот заголовок")



Файл `config.php`{.docutils .literal} должен содержать следующие значения:



    <?php



    // We choose to use Postgresql instead of Sqlite

    define('DB_DRIVER', 'postgres');



    // Mysql parameters

    define('DB_USERNAME', 'REPLACE_ME');

    define('DB_PASSWORD', 'REPLACE_ME');

    define('DB_HOSTNAME', 'REPLACE_ME');

    define('DB_NAME', 'kanboard');



Примечание: Вы можете переименовать демонстрационный файл `config.default.php`{.docutils .literal} в `config.php`{.docutils .literal}.



### Импортирование дампа SQL (альтернативный метод)[¶](#importing-sql-dump-alternative-method "Ссылка на этот заголовок")



В первый раз, Канборд запускает по очереди каждую миграцию базы данных и этот процес может занять некоторое время, в зависимости от вашей конфигурации.



Для избежания проблем или задержек вы можете инициализировать базу данных напрямую посредством импорта схемы SQL:



    psql -U postgres my_database < app/Schema/Sql/postgres.sql



Файл `app/Schema/Sql/postgres.sql`{.docutils .literal} - это sql дамп, который представляет последнюю версию базы данных.



### [Оглавление](index.markdown)



-   [Настройка Postgresql](#)

    -   [Требования](#requirements)

    -   [Настройка](#configuration)

        -   [Создайте пустую базу данных выполнив команду `pgsql`{.docutils .literal}:](#create-an-empty-database-with-the-command-pgsql)

        -   [Создание конфигурационного файла](#create-a-config-file)

        -   [Импортирование дампа SQL (альтернативный метод)](#importing-sql-dump-alternative-method)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/postgresql-configuration.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/postgresql-configuration.txt)

