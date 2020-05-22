
$script = <<SCRIPT
apt-get update
apt-get install -y apache2 libapache2-mod-php7.4 php7.4-cli php7.4-mbstring php7.4-sqlite3 php7.4-zip \
    php7.4-opcache php7.4-json php7.4-mysql php7.4-pgsql php7.4-ldap php7.4-gd php7.4-xml php7.4-curl && \
apt-get clean && \
echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf && \
a2enmod rewrite

service apache2 restart

rm -f /var/www/html/index.html
date > /etc/vagrant_provisioned_at

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

cd /var/www/html && composer install

SCRIPT

Vagrant.configure("2") do |config|
  config.vm.boot_timeout = 1200
  config.vm.define "ubuntu" do |m|
    m.vm.box = "ubuntu/focal64"
    m.vm.provision "shell", inline: $script
    m.vm.synced_folder ".", "/var/www/html", owner: "www-data", group: "www-data"
    m.vm.network :forwarded_port, guest: 80, host: 8001
  end
end
