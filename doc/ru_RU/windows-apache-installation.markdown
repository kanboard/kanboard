Установка Канборд на Windows Server и Apache
============================================



Это руководство поможет вам шаг за шагом установить Канборд на Windows Server с Apache и PHP



**Внимание**: Если у вас 64 разрядная платформа, то вам нужно выбрать “x64”, и выберите “x86” для 32 разрядной операционной системы.



Установка распространяемого пакета Visual C++[¶](#visual-c-redistributable-installation "Ссылка на этот заголовок")
-------------------------------------------------------------------------------------------------------------------



PHP и Apache скомпилированы с Visual Studio, поэтому вам нужно установить эту библиотеку, если вы не сделали это ранее.



1.  Скачайте библиотеку с [официального вебсайта Microsoft](http://www.microsoft.com/en-us/download/details.aspx?id=30679)



2.  Запустите установку `vcredist_x64.exe` или `vcredist_x86.exe`, в соответствии с вашей платформой



Установка Apache[¶](#apache-installation "Ссылка на этот заголовок")
--------------------------------------------------------------------



1.  Скачайте исходники Apache с [Apache Lounge](http://www.apachelounge.com/download/)



2.  Разархивируйте Apache24 в каталог `C:\Apache24`



### Назначение имени сервера[¶](#define-the-server-name "Ссылка на этот заголовок")



Откройте файл `C:\Apache24\conf\httpd.conf` и добавьте директиву:



    ServerName localhost



### Установка сервиса Apache[¶](#install-the-apache-service "Ссылка на этот заголовок")



Откройте консоль (`cmd.exe`), перейдите в каталог `C:\Apache24\bin` и установите сервис Apache:



    cd C:\Apache24\bin



    # Install the windows service

    httpd.exe -k install



### Установка ApacheMonitor[¶](#install-apachemonitor "Ссылка на этот заголовок")



-   Выполните `C:\Apache24\bin\ApacheMonitor.exe` и добавьте его в автозагрузку.



-   Теперь во всплывающем меню, при нажатии правой кнопки мыши на иконке, нажмите запустить Apache



### Проверка работы Apache[¶](#check-the-apache-installation "Ссылка на этот заголовок")



В браузере откройте <http://localhost/>. Вы должны увидеть пустую страницу и текст “It works!”.



Установка PHP[¶](#php-installation "Ссылка на этот заголовок")
--------------------------------------------------------------



1.  Скачайте последнюю стабильную версию PHP с [официального сайта PHP](http://windows.php.net/download/), выберите версию **Thread Safe** и используйте соответствующую разрядность: x86 or x64.



2.  Разархивируйте файлы в `C:\php`



3.  Перейдите в каталог PHP (`C:\php`) и переименуйе файл `php.ini-production` в `php.ini`



Отредактируйте `php.ini`:



Раскоментируйте директорию расширений:



    extension_dir = "C:/php/ext"



Раскоментируйте следующие модули PHP:



    extension=php_gd2.dll

    extension=php_ldap.dll

    extension=php_mbstring.dll

    extension=php_openssl.dll

    extension=php_pdo_sqlite.dll



Установите часовой пояс:



    date.timezone = America/Montreal



Список всех поддерживаемых часовых поясов можно посмотреть в [документации PHP](http://php.net/manual/en/timezones.america.php).



Загрузка модулей PHP для Apache:



Добавьте следующие строки конфигурации в файл `C:\Apache24\conf\httpd.conf`:



    LoadModule php5_module "c:/php/php5apache2_4.dll"

    AddHandler application/x-httpd-php .php



    # configure the path to php.ini

    PHPIniDir "C:/php"



    # change this directive

    DirectoryIndex index.php index.html



Перезапустите Apache.



Проверка работы PHP:



Создайте файл `phpinfo.php` в каталоге `C:\Apache24\htdocs`:



    <?php



    phpinfo();



    ?>



Откройте в браузере [http://localhost/phpinfo.php](http://localhost/phpinfo.php) и вы должны увидеть информацию о PHP.



Устновка Канборд[¶](#kanboard-installation "Ссылка на этот заголовок")
----------------------------------------------------------------------



-   [Скачайте zip файл](https://kanboard.net/downloads)



-   Разархивируйте архив в `C:\Apache24\htdocs\kanboard`



-   Откройте в браузере <http://localhost/kanboard/>. Ура. Теперь вы можете работать в Канборд. Все легко и просто.



-   Учетная запись и пароль по умолчанию - **admin/admin**



Протестировано на[¶](#tested-configuration "Ссылка на этот заголовок")
----------------------------------------------------------------------



-   Windows 2008 R2 / Apache 2.4.12 / PHP 5.6.8



Примечание[¶](#notes "Ссылка на этот заголовок")
------------------------------------------------



-   Некоторые функции Канборда требуют выполнять [запуск ежедневных фоновых задач](cronjob.markdown).



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

