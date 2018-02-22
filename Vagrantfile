
$script = <<SCRIPT
apt-get update
apt-get install -y apache2 libapache2-mod-php7.0 php7.0-cli php7.0-mbstring php7.0-sqlite3 php7.0-zip \
    php7.0-opcache php7.0-json php7.0-mysql php7.0-pgsql php7.0-ldap php7.0-gd php7.0-xml php7.0-curl && \
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
