Как инсталировать Канборд на Ubuntu?
====================================



Ubuntu Xenial 16.04 LTS[¶](#ubuntu-xenial-16-04-lts "Ссылка на этот заголовок")
-------------------------------------------------------------------------------



Установите Apache и PHP:



    sudo apt-get update

    sudo apt-get install -y apache2 libapache2-mod-php7.0 php7.0-cli php7.0-mbstring php7.0-sqlite3 \

        php7.0-opcache php7.0-json php7.0-mysql php7.0-pgsql php7.0-ldap php7.0-gd



Установите Канборд:



    cd /var/www/html
    # Download the latest release from https://github.com/kanboard/kanboard/releases
    wget https://github.com/kanboard/kanboard/archive/v<version>.zip

    unzip kanboard-<version>.zip
    chown -R www-data:www-data kanboard-<version>/data
    rm kanboard-<version>.zip



Ubuntu Trusty 14.04 LTS[¶](#ubuntu-trusty-14-04-lts "Ссылка на этот заголовок")
-------------------------------------------------------------------------------



Установите Apache и PHP:



    sudo apt-get update

    sudo apt-get install -y php5 php5-sqlite php5-mysql php5-pgsql php5-ldap php5-gd php5-json php5-mcrypt unzip



Установите Канборд:



    cd /var/www/html

    # Download the latest release from https://github.com/kanboard/kanboard/releases
    wget https://github.com/kanboard/kanboard/archive/v<version>.zip

    unzip kanboard-<version>.zip
    chown -R www-data:www-data kanboard-<version>/data
    rm kanboard-<version>.zip



Ubuntu Precise 12.04 LTS[¶](#ubuntu-precise-12-04-lts "Ссылка на этот заголовок")
---------------------------------------------------------------------------------



Установите Apache и PHP:



    sudo apt-get update

    sudo apt-get install -y php5 php5-sqlite php5-mysql php5-pgsql php5-ldap php5-gd php5-json php5-mcrypt unzip



Установите Канборд:



    cd /var/www

    # Download the latest release from https://github.com/kanboard/kanboard/releases
    wget https://github.com/kanboard/kanboard/archive/v<version>.zip

    unzip kanboard-<version>.zip
    chown -R www-data:www-data kanboard-<version>/data
    rm kanboard-<version>.zip



Некоторые возможности Канборда требуют [запуска ежедневных фоновых задач](cronjob.markdown).




 



[Русская документация Kanboard](http://kanboard.ru/doc/)

