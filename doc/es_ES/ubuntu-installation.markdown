Como instalar Kanboard en Ubuntu?
==================================

Ubuntu Xenial 16.04 LTS
-----------------------

Instalar Apache y PHP:

```bash---terminal
sudo apt-get update
sudo apt-get install -y apache2 libapache2-mod-php7.0 php7.0-cli php7.0-mbstring php7.0-sqlite3 \
    php7.0-opcache php7.0-json php7.0-mysql php7.0-pgsql php7.0-ldap php7.0-gd
```

Instalar Kanboard:

```bash---terminal
cd /var/www/html
sudo wget https://kanboard.net/kanboard-latest.zip
sudo unzip kanboard-latest.zip
sudo chown -R www-data:www-data kanboard/data
sudo rm kanboard-latest.zip
```

Ubuntu Trusty 14.04 LTS
-----------------------

Instalar Apache y PHP:

```bash---terminal
sudo apt-get update
sudo apt-get install -y php5 php5-sqlite php5-mysql php5-pgsql php5-ldap php5-gd php5-json php5-mcrypt unzip
```

Instalar Kanboard:

```bash---terminal
cd /var/www/html
sudo wget https://kanboard.net/kanboard-latest.zip
sudo unzip kanboard-latest.zip
sudo chown -R www-data:www-data kanboard/data
sudo rm kanboard-latest.zip
```

Ubuntu Precise 12.04 LTS
------------------------

Instalar Apache ay PHP:

```bash---terminal
sudo apt-get update
sudo apt-get install -y php5 php5-sqlite php5-mysql php5-pgsql php5-ldap php5-gd php5-json php5-mcrypt unzip
```

Instalar Kanboard:

```bash---terminal
cd /var/www
sudo wget https://kanboard.net/kanboard-latest.zip
sudo unzip kanboard-latest.zip
sudo chown -R www-data:www-data kanboard/data
sudo rm kanboard-latest.zip
```

Algunas características de Kanboard requieren  ejecutar [un trabajo en segundo plano todos los días] (cronjob.markdown).
