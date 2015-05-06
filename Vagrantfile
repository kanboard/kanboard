
$script_sqlite = <<SCRIPT
apt-get update
apt-get install -y apache2 php5 php5-gd php5-curl php5-sqlite php5-xdebug
apt-get clean -y
echo "ServerName localhost" >> /etc/apache2/apache2.conf
service apache2 restart
rm -f /var/www/html/index.html
date > /etc/vagrant_provisioned_at
# install Composer
curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
# install PHPUnit
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
phpunit --version
# Set kanboard dir as working dir
echo "cd /var/www/html" >> /home/vagrant/.bashrc
SCRIPT

$script_mysql = <<SCRIPT
export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y apache2 php5 php5-gd php5-curl php5-mysql php5-xdebug mysql-server mysql-client
apt-get clean -y
echo "ServerName localhost" >> /etc/apache2/apache2.conf
service apache2 restart
service mysql restart
echo "create database kanboard;" | mysql -u root
rm -f /var/www/html/index.html
date > /etc/vagrant_provisioned_at
# install Composer
curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
# install PHPUnit
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
phpunit --version
# Set kanboard dir as working dir
echo "cd /var/www/html" >> /home/vagrant/.bashrc
SCRIPT

$script_postgres = <<SCRIPT
export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y apache2 php5 php5-gd php5-curl php5-pgsql php5-xdebug postgresql postgresql-contrib
apt-get clean -y
echo "ServerName localhost" >> /etc/apache2/apache2.conf
service apache2 restart
service postgresql restart
rm -f /var/www/html/index.html
date > /etc/vagrant_provisioned_at
# install Composer
curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
# install PHPUnit
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
phpunit --version
# Set kanboard dir as working dir
echo "cd /var/www/html" >> /home/vagrant/.bashrc
SCRIPT

Vagrant.configure("2") do |config|

  config.vm.define "sqlite" do |m|
    m.vm.box = "ubuntu/trusty64"
    m.vm.provision "shell", inline: $script_sqlite
    m.vm.synced_folder ".", "/var/www/html", owner: "www-data", group: "www-data"
  end

  config.vm.define "mysql" do |m|
    m.vm.box = "ubuntu/trusty64"
    m.vm.provision "shell", inline: $script_mysql
    m.vm.synced_folder ".", "/var/www/html", owner: "www-data", group: "www-data"
  end

  config.vm.define "postgres" do |m|
    m.vm.box = "ubuntu/trusty64"
    m.vm.provision "shell", inline: $script_postgres
    m.vm.synced_folder ".", "/var/www/html", owner: "www-data", group: "www-data"
  end

  config.vm.define "debian7" do |m|
    m.vm.box = "chef/debian-7.6"
    m.vm.provision "shell", inline: $script_sqlite
    m.vm.synced_folder ".", "/var/www", owner: "www-data", group: "www-data"
  end

  config.vm.define "debian6" do |m|
    m.vm.box = "chef/debian-6.0.10"
    m.vm.provision "shell", inline: $script_sqlite
    m.vm.synced_folder ".", "/var/www", owner: "www-data", group: "www-data"
  end

  config.vm.define "centos7" do |m|
    m.vm.box = "chef/centos-7.0"
    m.vm.synced_folder ".", "/var/www/html", owner: "apache", group: "apache"
  end

  config.vm.define "centos65" do |m|
    m.vm.box = "chef/centos-6.5"
    m.vm.synced_folder ".", "/var/www/html", owner: "apache", group: "apache"
  end

  config.vm.define "freebsd10" do |m|
    m.vm.box = "chef/freebsd-10.0"
    m.vm.synced_folder ".", "/usr/local/www/apache24/data", type: "rsync", owner: "www", group: "www"
  end

  config.vm.network :forwarded_port, guest: 80, host: 8001
  #config.vm.network "public_network", :bridge => "en0: Wi-Fi (AirPort)"
end
