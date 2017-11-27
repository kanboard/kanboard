Installation de Kanboard sur Debian
===================================

Debian 8 (Jessie)
-----------------

Installez Apache et PHP :

```bash
apt-get update
apt-get install -y php5 php5-sqlite php5-gd unzip
service apache2 restart
```

Installez Kanboard :

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

Installez Apache et PHP :

```bash
apt-get update
apt-get install -y php5 php5-sqlite php5-gd unzip
```

Installez Kanboard :

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

Installez Apache et PHP :

```bash
apt-get update
apt-get install -y libapache2-mod-php5 php5-sqlite php5-gd unzip
```

Installez Kanboard :

```bash
cd /var/www

# Download the latest release from https://github.com/kanboard/kanboard/releases
wget https://github.com/kanboard/kanboard/archive/v<version>.zip

unzip kanboard-<version>.zip
chown -R www-data:www-data kanboard-<version>/data
rm kanboard-<version>.zip
```
