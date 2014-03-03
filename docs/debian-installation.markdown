How to install Kanboard on Debian?
==================================

A quick setup procedure for Debian:

```bash
apt-get update
apt-get install -y php5 php5-sqlite

# If sqlite is not loaded by default, add the extension manually
echo 'extension=sqlite.so' >> /etc/php5/conf.d/sqlite.ini

cd /var/www/
wget http://kanboard.net/kanboard-VERSION.zip
unzip kanboard-VERSION.zip
chown -R www-data:www-data kanboard/data
```