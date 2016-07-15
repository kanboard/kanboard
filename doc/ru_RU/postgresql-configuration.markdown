Настройка Postgresql
====================



По умолчанию, Канборд использует для хранения данных Sqlite, но возможно использовать и Postgresql.



Требования[¶](#requirements "Ссылка на этот заголовок")
-------------------------------------------------------



-   Установленный и настроенный сервер Postgresql



-   Установленное PHP расширение - `pdo_pgsql` (Debian/Ubuntu: `apt-get install php5-pgsql`)



Примечание: работа Канборда протестирована с **Postgresql 9.3 и 9.4**



Настройка[¶](#configuration "Ссылка на этот заголовок")
-------------------------------------------------------



### Создайте пустую базу данных выполнив команду `pgsql`:[¶](#create-an-empty-database-with-the-command-pgsql "Ссылка на этот заголовок")



    CREATE DATABASE kanboard;



### Создание конфигурационного файла[¶](#create-a-config-file "Ссылка на этот заголовок")



Файл `config.php` должен содержать следующие значения:

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



Примечание: Вы можете переименовать демонстрационный файл `config.default.php` в `config.php`.



### Импортирование дампа SQL (альтернативный метод)[¶](#importing-sql-dump-alternative-method "Ссылка на этот заголовок")



В первый раз, Канборд запускает по очереди каждую миграцию базы данных и этот процес может занять некоторое время, в зависимости от вашей конфигурации.



Для избежания проблем или задержек вы можете инициализировать базу данных напрямую посредством импорта схемы SQL:

```bash
psql -U postgres my_database < app/Schema/Sql/postgres.sql
```

Файл `app/Schema/Sql/postgres.sql` - это sql дамп, который представляет последнюю версию базы данных.



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

