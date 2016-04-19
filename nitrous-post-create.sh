#!/bin/bash
rm -rf ~/code/public_html

sudo apt-get update
sudo apt-get install -y php5-sqlite
sudo apt-get clean

cd ~/code
mv kanboard public_html
cd public_html
composer install
cd ~/code
sudo chown -R nitrous:www-data public_html
sudo service apache2 reload
