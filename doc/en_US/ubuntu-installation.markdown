Kanboard Installation on Ubuntu
===============================

Ubuntu Xenial 16.04 LTS
-----------------------

Install Apache and PHP:

```bash
sudo apt-get update
sudo apt-get install -y apache2 libapache2-mod-php7.0 php7.0-cli php7.0-mbstring php7.0-sqlite3 \
    php7.0-opcache php7.0-json php7.0-mysql php7.0-pgsql php7.0-ldap php7.0-gd php-7.0-xml
```

Install Kanboard:

```bash
cd /var/www/html

# Download the latest release from https://github.com/kanboard/kanboard/releases
wget https://github.com/kanboard/kanboard/archive/v<version>.zip

unzip kanboard-<version>.zip
chown -R www-data:www-data kanboard-<version>/data
rm kanboard-<version>.zip
```

- You might need to enable PHP extensions with the command `phpenmod`.
- Some features of Kanboard require that you run [a daily background job](cronjob.markdown).
