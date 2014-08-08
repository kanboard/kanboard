How to install Kanboard on Ubuntu?
==================================

Ubuntu 14.04 LTS
----------------

Install Apache and PHP:

```bash
sudo apt-get update
sudo apt-get install -y php5 php5-sqlite unzip
```

In case your webserver was running restart to make sure the php modules are reloaded

```bash
service apache2 restart
```

Install Kanboard:

```bash
cd /var/www/html
sudo wget http://kanboard.net/kanboard-latest.zip
sudo unzip kanboard-latest.zip
sudo chown -R www-data:www-data kanboard/data
sudo rm kanboard-latest.zip
```
