#!/bin/bash

#auto-installer for ubuntu 18 only
# type chmod 777 install_ubuntu_18.sh
#make sure to run `sudo install_ubuntu_18.sh`
#Some more helper commands to get up and running quickly




sudo sudo apt install tasksel
sudo tasksel install lamp-server



sudo apt install git

cd /var/
cd www/
cd html/

ifconfig
sudo apt install ssh


service ssh status
sudo systemctl enable ssh

git clone https://github.com/kanboard/kanboard.git

mv kanboard/ /var/www/html/
sudo mv kanboard/ /var/www/html/
cd /var/www/html/

cd kanboard/

find /var/www/html/  -type d -name "data"

chmod +w data/
chmod 777 data/

cd /var/www/html/
ls
cd kanboard/


sudo apt-get install php-sqlite
sudo apt-get install sqlite
sudo apt-get install php7.0


sudo apt-get install php7.0-gd

sudo apt-get install php7.0-mbstring

sudo apt-get install php7.0-dom
sudo service apache2 restart
sudo reboot
