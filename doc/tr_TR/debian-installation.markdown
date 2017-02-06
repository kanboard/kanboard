Debian'da Kanboard Kurulumu
===============================

Debian 8 (Jessie)
-----------------

Apache ve PHP'yi kurun:

```bash
apt-get update
apt-get install -y php5 php5-sqlite php5-gd unzip
service apache2 restart
```

Kanboard'u kurun:

```bash
cd /var/www/html
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R www-data:www-data kanboard/data
rm kanboard-latest.zip
```

Debian 7 (Wheezy)
-----------------

Apache ve PHP'yi kurun:
```bash
apt-get update
apt-get install -y php5 php5-sqlite php5-gd unzip
```

Kanboard'u kurun:

```bash
cd /var/www
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R www-data:www-data kanboard/data
rm kanboard-latest.zip
```

Debian 6 (Squeeze)
------------------

Apache ve PHP'yi kurun:

```bash
apt-get update
apt-get install -y libapache2-mod-php5 php5-sqlite php5-gd unzip
```

Kanboard'u kurun:

```bash
cd /var/www
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R www-data:www-data kanboard/data
rm kanboard-latest.zip
```

Not
-----

Kanboard'un bazı özellikleri, [günlük arka plan işi-cronjob](cronjob.markdown) çalıştırmanızı gerektirir.
