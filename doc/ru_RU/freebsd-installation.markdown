Инсталяция на FreeBSD 10
========================


Инсталяция из пакетов[¶](#install-from-packages "Ссылка на этот заголовок")
---------------------------------------------------------------------------

    $ pkg update

    $ pkg upgrade

    $ pkg install apache24 mod_php56 kanboard



Включите Apache в `/etc/rc.conf`{.docutils .literal}:



    $ echo apache24_enable="YES" >> /etc/rc.conf



Установите PHP для Apache:



    $ echo "AddType application/x-httpd-php .php" >> /usr/local/etc/apache24/Includes/php.conf

    $ echo "DirectoryIndex index.php index.html" >> /usr/local/etc/apache24/Includes/php.conf



Затем, запустите Apache:



    $ service apache24 start



Создайте символическую ссылку на каталог Kanboard в корне Apache:



    cd /usr/local/www/apache24/data

    ln -s /usr/local/www/kanboard



Готово. Можете перейти в <http:/>/вашвебсервер/kanboard и начинать работать!



*Примечание*: Если вы хотите добавить дополнительные возможности, типа интеграции LDAP, то нужно установить соответствующий PHP модуль. Также, вам необходимо настроить соответсвующие права на каталог data.



Установка из портов[¶](#installing-from-ports "Ссылка на этот заголовок")
-------------------------------------------------------------------------


Нужно установить 3 основных элемента:



-   Apache

-   mod\_php for Apache

-   Kanboard



Загрузите и распакуйте порты:



    $ portsnap fetch

    $ portsnap extract



или обновите имеющиеся:



    $ portsnap fetch

    $ portsnap update



Дополнительную информацию о дереве портов вы можете посмотреть на [FreeBSD Handbook](https://www.freebsd.org/doc/handbook/ports-using.html).



Установка Apache:



    $ cd /usr/ports/www/apache24

    $ make install clean



Включите Apache в `/etc/rc.conf`{.docutils .literal}:



    $ echo apache24_enable="YES" >> /etc/rc.conf



Установите mod\_php для Apache:



    $ cd /usr/ports/www/mod_php5

    $ make install clean



Установите Kanboard из портов:



    $ cd /usr/ports/www/kanboard

    $ make install clean



Установите PHP для Apache:



    $ echo "AddType application/x-httpd-php .php" >> /usr/local/etc/apache24/Includes/php.conf

    $ echo "DirectoryIndex index.php index.html" >> /usr/local/etc/apache24/Includes/php.conf



Затем, запустите Apache:



    $ service apache24 start



Готово. Можете перейти в <http:/>/вашвебсервер/kanboard и начинать работать!



*Примечание*: Если вы хотите использовать дополнительные возможности, типа интеграции LDAP, то нужно установить PHP модуль из `lang/php5-extensions`{.docutils .literal}.



Установка из архива[¶](#manual-installation "Ссылка на этот заголовок")
-----------------------------------------------------------------------

Начина с версии 1.0.16 Kanboard имеется в портах FreeBSD, поэтому нет необходимости устанавливать вручную.



Обратите внимание[¶](#please-note "Ссылка на этот заголовок")
-------------------------------------------------------------

-   Порт расположен на хостинге [bitbucket](https://bitbucket.org/if0/freebsd-kanboard/). Делайте комментарии, ответвления и предлагайте обновления!
-   Некоторые возможности Канборд требуют [запуск ежедневных фоновых задач](cronjob.markdown).


 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

