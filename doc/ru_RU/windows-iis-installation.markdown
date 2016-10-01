Инсталяция Kanboard на Windows 2008/2012 с IIS
==============================================



Это пошаговое руководство поможет вам установить Канборд на Windows Server с IIS и PHP.



Установка PHP[¶](#php-installation "Ссылка на этот заголовок")
--------------------------------------------------------------



-   Установите IIS на ваш Windows сервер (Добавьте новую роль и не забудьте включить CGI/FastCGI)



-   При инсталяции PHP можете использовать следующую официальную документацию:



    -   [Microsoft IIS 5.1 and IIS 6.0](http://php.net/manual/en/install.windows.iis6.php)

    -   [Microsoft IIS 7.0 and later](http://php.net/manual/en/install.windows.iis7.php)

    -   [PHP for Windows is available here](http://windows.php.net/download/)



Отредактируйте `php.ini`, раскоментируйте эти PHP модули:



    extension=php_gd2.dll

    extension=php_ldap.dll

    extension=php_mbstring.dll

    extension=php_openssl.dll

    extension=php_pdo_sqlite.dll



Установите часовой пояс



    date.timezone = America/Montreal



Список поддерживаемых часовых поясов можно посмотреть в [документации PHP](http://php.net/manual/en/timezones.america.php).



Проверьте, что PHP работает корректно:



Перейдите в корневой каталог IIS `C:\inetpub\wwwroot` и создайте файл `phpinfo.php`, со следующим содержимым:



    <?php



    phpinfo();



    ?>



В браузере откройте страницу `http://localhost/phpinfo.php` и вы должны увидеть текущие настройки PHP. Если вы видите ошибку 500, значит что-то сделано неправильно при установке.



Примечание:



-   Если вы используете PHP \< 5.4, то необходимо включить короткие теги (short tags) в php.ini



-   Не забудьте включить необходимые php расширения, упомянутые выше



-   Если вы наблюдаете ошибку “the library MSVCP110.dll is missing”, то возможно вам нужно скачать распространяемый пакет Visual C++ для Visual Studio с сайта Microsoft.



Установка Канборд[¶](#kanboard-installation "Ссылка на этот заголовок")
-----------------------------------------------------------------------



-   Скачайте zip файл



-   Распакуйте архив в `C:\inetpub\wwwroot\kanboard` (например)



-   Убедитесь, что у пользователя вебсервера IIS имеется доступ на запись на директорию `data`



-   Откройте веб браузер и используйте Kanboard <http://localhost/kanboard/>



-   Пользователь и пароль по умолчанию - **admin/admin**



Работа Канборд тестировалось на[¶](#tested-configurations "Ссылка на этот заголовок")
-------------------------------------------------------------------------------------



-   Windows 2008 R2 Standard Edition / IIS 7.5 / PHP 5.5.16

-   Windows 2012 Standard Edition / IIS 8.5 / PHP 5.3.29



Примечание[¶](#notes "Ссылка на этот заголовок")
------------------------------------------------



-   Некоторые возможности Канборда требуют [запуск выполнения ежедневных фоновых задач](cronjob.markdown).




 



[Русская документация Kanboard](http://kanboard.ru/doc/)

