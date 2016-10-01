Настройка Mysql/MariaDB
=======================



По умолчанию Канборд использует для хранения данных Sqlite. Вместо Sqlite возможно использовать Mysql или MariaDB.



Требования[¶](#requirements "Ссылка на этот заголовок")
-------------------------------------------------------



-   Сервер Mysql



-   Установленное расширение PHP - `pdo_mysql`



Примечание: работа Канборда протестирована с **Mysql \>= 5.5 и MariaDB \>= 10.0**



Настройка Mysql[¶](#mysql-configuration "Ссылка на этот заголовок")
-------------------------------------------------------------------



### Создание базы данных[¶](#create-a-database "Ссылка на этот заголовок")



Первым шагом надо создать базу данных на вашем сервере Mysql. Например, вы можете создать базу в командной строке клиента mysql:



    CREATE DATABASE kanboard;



### Создание файла конфигурации[¶](#create-a-config-file "Ссылка на этот заголовок")



Файл `config.php` должен содержать следующие значения:



    <?php



    // We choose to use Mysql instead of Sqlite

    define('DB_DRIVER', 'mysql');



    // Mysql parameters

    define('DB_USERNAME', 'REPLACE_ME');

    define('DB_PASSWORD', 'REPLACE_ME');

    define('DB_HOSTNAME', 'REPLACE_ME');

    define('DB_NAME', 'kanboard');



Примечание: Вы можете переименовать демонстрационный файл `config.default.php` в `config.php`.



### Импорт SQL дампа (альтернативный метод)[¶](#importing-sql-dump-alternative-method "Ссылка на этот заголовок")



В первый раз, Канборд запускает по очереди каждую миграцию базы данных и этот процес может занять некоторое время, в зависимости от вашей конфигурации.



Чтобы избежать задержек, вы можете инициализировать базу данных напрямую, имопртируя SQL схему:



    mysql -u root -p my_database < app/Schema/Sql/mysql.sql



Файл [\`\`](#id1)app/Schema/Sql/mysql.sql\`\`это SQL дамп, который представляет последнюю версию базы данных.



Конфигурация SSL[¶](#ssl-configuration "Ссылка на этот заголовок")
------------------------------------------------------------------



Эти параметры должны быть указаны для включения соединения Mysql SSL:



    // Mysql SSL key

    define('DB_SSL_KEY', '/path/to/client-key.pem');



    // Mysql SSL certificate

    define('DB_SSL_CERT', '/path/to/client-cert.pem');



    // Mysql SSL CA

    define('DB_SSL_CA', '/path/to/ca-cert.pem');





[Русская документация Kanboard](http://kanboard.ru/doc/)

