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

# Download the latest release from https://github.com/kanboard/kanboard/releases
wget https://github.com/kanboard/kanboard/archive/v<version>.zip

unzip kanboard-<version>.zip
chown -R www-data:www-data kanboard-<version>/data
rm kanboard-<version>.zip
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

# Download the latest release from https://github.com/kanboard/kanboard/releases
wget https://github.com/kanboard/kanboard/archive/v<version>.zip

unzip kanboard-<version>.zip
chown -R www-data:www-data kanboard-<version>/data
rm kanboard-<version>.zip
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

# Download the latest release from https://github.com/kanboard/kanboard/releases
wget https://github.com/kanboard/kanboard/archive/v<version>.zip

unzip kanboard-<version>.zip
chown -R www-data:www-data kanboard-<version>/data
rm kanboard-<version>.zip
```

Not
-----

Kanboard'un bazı özellikleri, [günlük arka plan işi-cronjob](cronjob.markdown) çalıştırmanızı gerektirir.
