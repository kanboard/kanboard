Installation on OpenSuse
========================

OpenSuse Leap 42.1
------------------

```bash
sudo zypper install php5 php5-sqlite php5-gd php5-json php5-mcrypt php5-mbstring php5-openssl
cd /srv/www/htdocs
sudo wget https://kanboard.net/kanboard-latest.zip
sudo unzip kanboard-latest.zip
sudo chown -R wwwrun /srv/www/htdocs/kanboard
sudo rm kanboard-latest.zip
```
