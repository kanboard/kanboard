Аутентификация Reverse Proxy
============================



Этот метод аутентификации часто используется для [SSO](https://ru.wikipedia.org/wiki/%D0%A2%D0%B5%D1%85%D0%BD%D0%BE%D0%BB%D0%BE%D0%B3%D0%B8%D1%8F_%D0%B5%D0%B4%D0%B8%D0%BD%D0%BE%D0%B3%D0%BE_%D0%B2%D1%85%D0%BE%D0%B4%D0%B0) (Технология единого входа), особенно удобно в больших организациях.



Аутентификация выполняется с помощью другой системы, поэтому Канборд не знает вашего пароля и допускает вас к приложению, так как вы уже прошли аутентификацию.



Требования[¶](#requirements "Ссылка на этот заголовок")
-------------------------------------------------------



-   Правильно сконфигурированный reverse proxy



или



-   Apache Auth на том же сервере



Как это работает?[¶](#how-does-this-work "Ссылка на этот заголовок")
--------------------------------------------------------------------



1.  Ваш reverse proxy аутентифицирует пользователя и посылает имя пользователя через заголовок HTTP.



2.  Канборд извлекает имя пользователя из запроса



    -   Пользователь создается в Канборд автоматически (опция настраивается)



    -   Открывается новая сессия Канборд (дополнительная аутентификация в Канборд не нужна)



Инструкция по установке[¶](#installation-instructions "Ссылка на этот заголовок")
---------------------------------------------------------------------------------



### Настройка вашего reverse proxy[¶](#setting-up-your-reverse-proxy "Ссылка на этот заголовок")



В рамках данной документации не рассматривается установка и настройка reverse proxy. Вы должны убедится, что логин пользователя отправляется с reverse proxy в заголовке HTTP.



### Настройки Канборда[¶](#setting-up-kanboard "Ссылка на этот заголовок")



Создайте свой файл конфигурации `config.php` или скопируйте конфигурацию из файла `config.default.php`:



    <?php



    // Enable/disable reverse proxy authentication

    define('REVERSE_PROXY_AUTH', true); // Set this value to true



    // The HTTP header to retrieve. If not specified, REMOTE_USER is the default

    define('REVERSE_PROXY_USER_HEADER', 'REMOTE_USER');



    // The default Kanboard admin for your organization.

    // Since everything should be filtered by the reverse proxy,

    // you should want to have a bootstrap admin user.

    define('REVERSE_PROXY_DEFAULT_ADMIN', 'myadmin');



    // The default domain to assume for the email address.

    // In case the username is not an email address, it

    // will be updated automatically as USER@mydomain.com

    define('REVERSE_PROXY_DEFAULT_DOMAIN', 'mydomain.com');



Примечание:



-   Если proxy находится на том же сервере, что и Канборд, то в соответствии с протоколом \<[http://www.ietf.org/rfc/rfc3875](http://www.ietf.org/rfc/rfc3875)\>\`\_\_ имя заголовка будет `REMOTE_USER`. Например, Apache добавляет `REMOTE_USER` по умолчанию, если установлено `Require valid-user`.



-   Если Apache служит reverse proxy для другого Apache выполняющего Канборд, то заголовок `REMOTE_USER` не установлен (это же относится к IIS и Nginx).



-   Если у вас имеется действующий reverse proxy, то [проект HTTP ICAP](http://tools.ietf.org/html/draft-stecher-icap-subid-00#section-3.4) предполагает, что заголовок должен быть `X-Authenticated-User`. Этот стандарт де-факто был принят разными инструментами.



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

