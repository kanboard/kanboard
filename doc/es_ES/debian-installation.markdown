Como instalar Kanboard en debian
================================

Nota: Algunas caracteristicas de Kanboard requieren que tu corras [un job en background diariamente](cronjob.markdown).

Debian 8 (Jessie)
-----------------

Instalar Apache y PHP :

```bash
apt-get update
apt-get install -y php5 php5-sqlite php5-gd unzip
service apache2 restart
```

Instalar Kanboard

```bash
cd /var/www/html
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R www-data:www-data kanboard/data
rm kanboard-latest.zip
```

Debian 7 (Wheezy)
-----------------

Instalar Apache y PHP

```bash
apt-get update
apt-get install -y php5 php5-sqlite php5-gd unzip
```

Instalar Kanboard

```bash
cd /var/www
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R www-data:www-data kanboard/data
rm kanboard-latest.zip
```

Debian 6 (Squeeze)
------------------

Instalar Apache y PHP

```bash
apt-get update
apt-get install -y libapache2-mod-php5 php5-sqlite php5-gd unzip
```

Instalar Kanboard:

```bash
cd /var/www
wget https://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R www-data:www-data kanboard/data
rm kanboard-latest.zip
```
