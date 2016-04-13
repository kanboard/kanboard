
$script = <<SCRIPT
apt-get install -y apache2 php5 php5-sqlite php5-mysql php5-pgsql php5-gd curl unzip && \
apt-get clean && \
echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf && \
a2enmod rewrite

service apache2 restart

rm -f /var/www/html/index.html
date > /etc/vagrant_provisioned_at

wget -q https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit

SCRIPT

Vagrant.configure("2") do |config|

  config.vm.define "ubuntu" do |m|
    m.vm.box = "ubuntu/trusty64"
    m.vm.provision "shell", inline: $script
    m.vm.synced_folder ".", "/var/www/html", owner: "www-data", group: "www-data"
    m.vm.network :forwarded_port, guest: 80, host: 8001
  end
end
