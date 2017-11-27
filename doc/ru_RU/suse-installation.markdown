Инсталяция на OpenSuse
======================



OpenSuse Leap 42.1[¶](#opensuse-leap-42-1 "Ссылка на этот заголовок")
---------------------------------------------------------------------



    sudo zypper install php5 php5-sqlite php5-gd php5-json php5-mcrypt php5-mbstring php5-openssl

    cd /srv/www/htdocs

    # Download the latest release from https://github.com/kanboard/kanboard/releases
    sudo wget https://github.com/kanboard/kanboard/archive/v<VERSION>.zip

    sudo unzip kanboard-<version>.zip
    sudo chown -R wwwrun /srv/www/htdocs/kanboard-<version>/data
    sudo rm kanboard-<version>.zip

 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

