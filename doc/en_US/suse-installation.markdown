Installation on OpenSuse
========================

OpenSuse Leap 42.3
------------------

```bash
# install required packages
sudo zypper install apache2-mod_php7 php7-openssl php7-gd php7-mbstring php7-mcrypt php7-mysql php7-xmlrpc php7-ctype php7-json

# enable php7
sudo a2enmod php7

cd /srv/www/htdocs

# Download the latest release from https://github.com/kanboard/kanboard/releases

sudo wget https://github.com/kanboard/kanboard/archive/v<version>.zip
sudo unzip kanboard-<version>.zip

# Add permissions
sudo chown -R wwwrun /srv/www/htdocs/kanboard-<version>/app/data

# restart apache
sudo rcapache2 restart

# cleanup
sudo rm kanboard-<version>.zip
```

