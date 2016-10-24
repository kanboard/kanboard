Installation de Kanboard sur Ubuntu
===================================

Ubuntu Xenial 16.04 LTS
-----------------------

Installez Apache et PHP :

```bash
sudo apt-get update
sudo apt-get install -y apache2 libapache2-mod-php7.0 php7.0-cli php7.0-mbstring php7.0-sqlite3 \
    php7.0-opcache php7.0-json php7.0-mysql php7.0-pgsql php7.0-ldap php7.0-gd
```

Installez Kanboard :

```bash
cd /var/www/html
sudo wget https://kanboard.net/kanboard-latest.zip
sudo unzip kanboard-latest.zip
sudo chown -R www-data:www-data kanboard/data
sudo rm kanboard-latest.zip
```

Ubuntu Trusty 14.04 LTS
-----------------------

Installez Apache et PHP :

```bash
sudo apt-get update
sudo apt-get install -y php5 php5-sqlite php5-mysql php5-pgsql php5-ldap php5-gd php5-json php5-mcrypt unzip
```

Installez Kanboard :

```bash
cd /var/www/html
sudo wget https://kanboard.net/kanboard-latest.zip
sudo unzip kanboard-latest.zip
sudo chown -R www-data:www-data kanboard/data
sudo rm kanboard-latest.zip
```

Ubuntu Precise 12.04 LTS
------------------------

Installez Apache et PHP :

```bash
sudo apt-get update
sudo apt-get install -y php5 php5-sqlite php5-mysql php5-pgsql php5-ldap php5-gd php5-json php5-mcrypt unzip
```

Installez Kanboard :

```bash
cd /var/www
sudo wget https://kanboard.net/kanboard-latest.zip
sudo unzip kanboard-latest.zip
sudo chown -R www-data:www-data kanboard/data
sudo rm kanboard-latest.zip
```

Certaines fonctionnalités de Kanboard demandent à ce que vous installiez une [tâche planifiée](cronjob.markdown).
