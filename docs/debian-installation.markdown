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
wget http://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R www-data:www-data kanboard/data
rm kanboard-latest.zip
```

Debian 6 (Squeeze)
------------------

**Kanboard >= 1.0.10 require at least PHP 5.3.7 and Debian 6 provide PHP 5.3.3 by default**

Install Apache and PHP:

```bash
apt-get update
apt-get install -y libapache2-mod-php5 php5-sqlite unzip
```

Install Kanboard:

```bash
cd /var/www
wget http://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R www-data:www-data kanboard/data
rm kanboard-latest.zip
```
