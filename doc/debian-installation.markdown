Kanboard Installation on Debian
===============================

Debian 8 (Jessie)
-----------------

Install Apache and PHP:

```bash
apt-get update
apt-get install -y php5 php5-sqlite php5-gd unzip
service apache2 restart
```

Install Kanboard:

```bash
cd /var/www/html
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R www-data:www-data kanboard/data
rm kanboard-latest.zip
```

Debian 7 (Wheezy)
-----------------

Install Apache and PHP:

```bash
apt-get update
apt-get install -y php5 php5-sqlite php5-gd unzip
```

Install Kanboard:

```bash
cd /var/www
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R www-data:www-data kanboard/data
rm kanboard-latest.zip
```

Debian 6 (Squeeze)
------------------

Install Apache and PHP:

```bash
apt-get update
apt-get install -y libapache2-mod-php5 php5-sqlite php5-gd unzip
```

Install Kanboard:

```bash
cd /var/www
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R www-data:www-data kanboard/data
rm kanboard-latest.zip
```

Notes
-----

Some features of Kanboard require that you run [a daily background job](cronjob.markdown).
