How to install Kanboard on Debian?
==================================

Debian 7 (Wheezy)
-----------------

Install Apache and PHP:

```bash
apt-get update
apt-get install -y php5 php5-sqlite unzip
```

Install Kanboard:

```bash
cd /var/www
wget http://kanboard.net/kanboard-VERSION.zip
unzip kanboard-VERSION.zip
chown -R www-data:www-data kanboard/data
rm kanboard-VERSION.zip
```

Debian 6 (Squeeze)
------------------

Install Apache and PHP:

```bash
apt-get update
apt-get install -y libapache2-mod-php5 php5-sqlite unzip
```

Install Kanboard:

```bash
cd /var/www
wget http://kanboard.net/kanboard-VERSION.zip
unzip kanboard-VERSION.zip
chown -R www-data:www-data kanboard/data
rm kanboard-VERSION.zip
```