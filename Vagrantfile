
$script = <<SCRIPT
sudo add-apt-repository ppa:ondrej/php
#sudo add-apt-repository ppa:ondrej/apache2
apt-get update
apt-get install -y apache2 php5.6 php5.6-sqlite php5.6-mysql php5.6-pgsql php5.6-gd curl unzip php5.6-curl php5.6-ldap php5.6-mbstring php5.6-dom php5.6-simplexml php5.6-xml && \
apt-get clean && \
echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf && \
a2enmod rewrite

service apache2 restart

rm -f /var/www/html/index.html
date > /etc/vagrant_provisioned_at

curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

cd /var/www/html && composer install

wget -q https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit

SCRIPT

Vagrant.configure("2") do |config|

  config.vm.define "ubuntu" do |m|
    m.vm.box = "ubuntu/xenial64"
    m.vm.provision "shell", inline: $script
    m.vm.synced_folder ".", "/var/www/html", owner: "www-data", group: "www-data"
    m.vm.network :forwarded_port, guest: 80, host: 8001
  end
end
