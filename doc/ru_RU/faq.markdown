Часто задаваемые вопросы
========================


Вы можете порекомендовать веб хостинг провайдера для Канборд?[¶](#can-you-recommend-a-web-hosting-provider-for-kanboard "Ссылка на этот заголовок")
---------------------------------------------------------------------------------------------------------------------------------------------------

Работу Канборд поддерживают несколько крупных провайдеров VPS, такие как [Digital Ocean](https://www.digitalocean.com/?refcode=4b541f47aae4), [Linode](https://www.linode.com/?r=4e381ac8a61116f40c60dc7438acc719610d8b11) или [Gandi](https://www.gandi.net/).

Для получения большей производительности, выбирайте провайдера с быстрыми дисками чтения/записи, потому что Канборд использует по умолчанию Sqlite. Избегайте провайдеров которые используют подключения NFS.


У меня выводится пустая страница после установки или обновления Канборд[¶](#i-get-a-blank-page-after-installing-or-upgrading-kanboard "Ссылка на этот заголовок")
-----------------------------------------------------------------------------------------------------------------------------------------------------------------

-   Проверьте, установили ли вы всё на сервер, что было указано в требованиях
-   Посмотрите ошибки в PHP и Apache логах
-   Проверьте права доступа к файлам
-   Если вы используете кеширование OPcode, перезапустите ваш веб сервер или php-fpm


У меня выводится ошибка “There is no suitable CSPRNG installed on your system”[¶](#i-have-the-error-there-is-no-suitable-csprng-installed-on-your-system "Ссылка на этот заголовок")
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

Если вы используете PHP \< 7.0, то вам нужно включить расширение openssl или доступ из приложения к `/dev/urandom`, если имеются ограничения от `open_basedir`.


Страница не найдена и URL выглядит криво (&amp;)[¶](#page-not-found-and-the-url-seems-wrong-amp "Ссылка на этот заголовок")
---------------------------------------------------------------------------------------------------------------------------

-   UTL выглядит как `/?controller=auth&amp;action=login&amp;redirect_query=` вместо `?controller=auth&action=login&redirect_query=`
-   Канборд выдает ошибку “Страница не найдена”


Эта ошибка исходит из настроек конфигурации вашего PHP, значение `arg_separator.output` отсутствует в базовой настройке. Есть разные пути решения этой проблемы:

Измените значение прямо в вашем `php.ini`:


    arg_separator.output = "&"


Переделайте значение с помощью `.htaccess`:


    php_value arg_separator.output "&"


Иначе Канборд будет брать значение напрямую из PHP.



Ошибка аутентификации в API и Apache + PHP-FPM[¶](#authentication-failure-with-the-api-and-apache-php-fpm "Ссылка на этот заголовок")
-------------------------------------------------------------------------------------------------------------------------------------

По умолчанию, php-cgi под Apache не передает HTTP Basic user/pass в PHP. Чтобы это окружение заработало, добавьте эти строки в ваш файл `.htaccess`:



    RewriteCond %{HTTP:Authorization} ^(.+)$

    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]



Проблемы с eAccelerator[¶](#known-issues-with-eaccelerator "Ссылка на этот заголовок")
--------------------------------------------------------------------------------------

Канборд не очень хорошо работает с [eAccelerator](http://eaccelerator.net). Проблема в том, что выдается чистая страница или падает Apache:


    [Wed Mar 05 21:36:56 2014] [notice] child pid 22630 exit signal Segmentation fault (11)


Лучшее решение, чтобы избежать этой проблемы, выключить eAccelerator или прописать в конфиге какие файлы вы хотите кешировать (параметр `eaccelerator.filter`).



Проект [eAccelerator выглядит мертвым и не обновляется с 2012](https://github.com/eaccelerator/eaccelerator/commits/master). Мы рекомендуем перейти на последнюю версию PHP, потому что в него включен [OPcache](http://php.net/manual/en/intro.opcache.php).


Почему минимальная рекомендуемая версия PHP 5.3.3?[¶](#why-the-minimum-requirement-is-php-5-3-3 "Ссылка на этот заголовок")
---------------------------------------------------------------------------------------------------------------------------

Канборд использует функцию `password_hash()` для шифрования пароля, а эта функция доступна только для PHP \>= 5.5.

Однако, имеется back-port для [более ранних версий PHP](https://github.com/ircmaxell/password_compat#requirements). Эта библиотека требует минимум PHP 5.3.7 для корректной работы.

По всей видимости, патчи безопасности back-port имеются в Centos и Debian, поэтому PHP 5.3.3 подходит для работы Канборд.

Канборд v1.0.10 и v1.0.11 требует минимум PHP 5.3.7, но эти изменения возвращены на PHP 5.3.3 в Канборде \>= v1.0.12



Как проверить работу Канборда со встроенным веб-сервером PHP?[¶](#how-to-test-kanboard-with-the-php-built-in-web-server "Ссылка на этот заголовок")
---------------------------------------------------------------------------------------------------------------------------------------------------

Если вы не хотите устанавливать веб сервер типа Apache, то вы можете протестировать работу Канборда на [встроенном в PHP веб сервере](http://www.php.net/manual/en/features.commandline.webserver.php):


    unzip kanboard-VERSION.zip

    cd kanboard

    php -S localhost:8000

    open http://localhost:8000/



Как установить Канборд на Yunohost?[¶](#how-to-install-kanboard-on-yunohost "Ссылка на этот заголовок")
-------------------------------------------------------------------------------------------------------

[YunoHost](https://yunohost.org/) это серверная операционная система, цель которой предоставить хостинг для всех.

Отсюда можно [загрузить инсталяционный пакет Kanboard для Yunohost](https://github.com/mbugeia/kanboard_ynh).


Где я могу найти список связанных с Канборд проектов?[¶](#where-can-i-find-a-list-of-related-projects "Ссылка на этот заголовок")
---------------------------------------------------------------------------------------------------------------------------------

-   [Kanboard API python client by @freekoder]([https://github.com/freekoder/kanboard-py](https://github.com/freekoder/kanboard-py))

-   [Kanboard Presenter by David Eberlein](https://github.com/davideberlein/kanboard-presenter)

-   [CSV2Kanboard by @ashbike]([https://github.com/ashbike/csv2kanboard](https://github.com/ashbike/csv2kanboard))

-   [Kanboard for Yunohost by @mbugeia]([https://github.com/mbugeia/kanboard\_ynh](https://github.com/mbugeia/kanboard_ynh))

-   [Trello import script by @matueranet]([https://github.com/matueranet/kanboard-import-trello](https://github.com/matueranet/kanboard-import-trello))

-   [Chrome extension by Timo](https://chrome.google.com/webstore/detail/kanboard-quickmenu/akjbeplnnihghabpgcfmfhfmifjljneh?utm_source=chrome-ntp-icon), [Source code](https://github.com/BlueTeck/kanboard_chrome_extension)

-   [Python client script by @dzudek]([https://gist.github.com/fguillot/84c70d4928eb1e0cb374](https://gist.github.com/fguillot/84c70d4928eb1e0cb374))

-   [Shell script for SQLite to MySQL/MariaDB migration by @oliviermaridat]([https://github.com/oliviermaridat/kanboard-sqlite2mysql](https://github.com/oliviermaridat/kanboard-sqlite2mysql))

-   [Git hooks for integration with Kanboard by Gene Pavlovsky](https://github.com/gene-pavlovsky/kanboard-git-hooks)



Имеются ли руководства по Канборду на других языках?[¶](#are-there-some-tutorials-about-kanboard-in-other-languages "Ссылка на этот заголовок")
-----------------------------------------------------------------------------------------------------------------------------------------------

-   [Серия статей про Kanboard на немецком языке](http://demaya.de/wp/2014/07/kanboard-eine-jira-alternative-im-detail-installation/) .
-   [Русская документация по Канборд](http://kanboard.ru/doc/).



 






 



[Русская документация Kanboard](http://kanboard.ru/doc/)

