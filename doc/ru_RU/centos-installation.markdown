Инсталяция Канборд на Centos[¶](#centos-installation "Ссылка на этот заголовок")

================================================================================



**Внимание**: Некоторые возможности Канборда требуют [запуск ежедневных фоновых задач](cronjob.markdown).



Centos 7[¶](#centos-7 "Ссылка на этот заголовок")

-------------------------------------------------



Установите PHP и Apache:



    yum install -y php php-mbstring php-pdo php-gd unzip wget



По умолчанию, Centos 7 использует PHP 5.4.16 и Apache 2.4.6.



Перезапустите Apache:



    systemctl restart httpd.service



Установите Канборд:



    cd /var/www/html

    wget https://kanboard.net/kanboard-latest.zip

    unzip kanboard-latest.zip

    chown -R apache:apache kanboard/data

    rm kanboard-latest.zip



Если включен SELinux, убедитесь что пользователь веб сервера Apache имеет права на запись в директорию data:



    chcon -R -t httpd_sys_content_rw_t /var/www/html/kanboard/data



Убедитесь, что Канборд может посылать email сообщения и делать внешние сетевые запросы, например с SELinux:



    setsebool -P httpd_can_network_connect=1



Позволяет делать внешние подключения если используется LDAP, SMTP, Web hooks или другая интеграция.



Centos 6.x[¶](#centos-6-x "Ссылка на этот заголовок")

-----------------------------------------------------



Установите PHP и Apache:



    yum install -y php php-mbstring php-pdo php-gd unzip wget



По умолчанию, Centos 6.5 использует PHP 5.3.3 и Apache 2.2.15.



Включите короткие теги:



-   Отредактируйте файл `/etc/php.ini`{.docutils .literal}



-   Измените строку `short_open_tag = On`{.docutils .literal} (вместо `short_open_tag = Off`{.docutils .literal})



Перезапустите Apache:



    service httpd restart



Установите Канборд:



    cd /var/www/html

    wget https://kanboard.net/kanboard-latest.zip

    unzip kanboard-latest.zip

    chown -R apache:apache kanboard/data

    rm kanboard-latest.zip



Готово. Можете работать с Канборд. Откройте в браузере `http://ваш_сервер/kanboard/`{.docutils .literal}.



### [Оглавление](index.markdown)



-   [Инсталяция Канборд на Centos](#)

    -   [Centos 7](#centos-7)

    -   [Centos 6.x](#centos-6-x)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/centos-installation.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/centos-installation.txt)

