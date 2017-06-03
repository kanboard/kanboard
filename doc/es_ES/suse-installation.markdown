Instalacion en OpenSuse
========================

OpenSuse Leap 42.1
------------------

```bash---terminal
sudo zypper install php5 php5-sqlite php5-gd php5-json php5-mcrypt php5-mbstring php5-openssl
cd /srv/www/htdocs
sudo wgethttps://kanboard.net/kanboard-latest.zip
sudo unzip kanboard-latest.zip
sudo chmod -R 777 kanboard
sudo rm kanboard-latest.zip

