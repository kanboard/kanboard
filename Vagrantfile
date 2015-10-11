
$debian_script = <<SCRIPT
apt-get update
apt-get install -y apache2 php5 php5-gd php5-curl php5-sqlite php5-xdebug php5-ldap
apt-get clean -y

sudo sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

if [ -f /etc/apache2/sites-enabled/000-default ]; then
  sudo sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/sites-enabled/000-default
fi

sudo a2enmod rewrite
service apache2 restart

rm -f /var/www/html/index.html
date > /etc/vagrant_provisioned_at

wget -q https://getcomposer.org/composer.phar
chmod +x composer.phar
sudo mv composer.phar /usr/local/bin/composer

wget -q https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit

SCRIPT

$centos_script = <<SCRIPT
sudo yum update -y
sudo yum install -y httpd php php-cli php-gd php-ldap php-mbstring php-mysql php-pdo php-pgsql php-xml wget

sudo sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/httpd/conf/httpd.conf

if [ -x /usr/bin/systemctl ]; then
  sudo systemctl restart httpd
  sudo systemctl enable httpd
  sudo chcon -R -t httpd_sys_content_rw_t /var/www/html/data
  sudo setsebool -P httpd_can_network_connect=1
else
  sudo service httpd restart
  sudo chkconfig httpd on
fi

rm -f /var/www/html/index.html
date > /etc/vagrant_provisioned_at

wget -q https://getcomposer.org/composer.phar
chmod +x composer.phar
sudo mv composer.phar /usr/local/bin/composer

wget -q https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit

SCRIPT

Vagrant.configure("2") do |config|

  config.vm.define "ubuntu" do |m|
    m.vm.box = "ubuntu/trusty64"
    m.vm.provision "shell", inline: $debian_script
    m.vm.synced_folder ".", "/var/www/html", owner: "www-data", group: "www-data"
    m.vm.network :forwarded_port, guest: 80, host: 8001
  end

  config.vm.define "debian8" do |m|
    m.vm.box = "debian/jessie64"
    m.vm.provision "shell", inline: $debian_script
    m.vm.synced_folder ".", "/var/www/html", owner: "www-data", group: "www-data"
    m.vm.network :forwarded_port, guest: 80, host: 8002
  end

  config.vm.define "debian7" do |m|
    m.vm.box = "debian/wheezy64"
    m.vm.provision "shell", inline: $debian_script
    m.vm.synced_folder ".", "/var/www", owner: "www-data", group: "www-data"
    m.vm.network :forwarded_port, guest: 80, host: 8003
  end

  config.vm.define "debian6" do |m|
    m.vm.box = "bento/debian-6.0.10"
    m.vm.provision "shell", inline: $debian_script
    m.vm.synced_folder ".", "/var/www", owner: "www-data", group: "www-data"
    m.vm.network :forwarded_port, guest: 80, host: 8004
  end

  config.vm.define "centos7" do |m|
    m.vm.box = "centos/7"
    m.vm.provision "shell", inline: $centos_script
    m.vm.synced_folder ".", "/var/www/html", owner: "apache", group: "apache", type: "rsync",
      rsync__exclude: ".git/", rsync__auto: true
    m.vm.network :forwarded_port, guest: 80, host: 8005
  end

  config.vm.define "centos6" do |m|
    m.vm.box = "bento/centos-6.7"
    m.vm.provision "shell", inline: $centos_script
    m.vm.synced_folder ".", "/var/www/html", owner: "apache", group: "apache", type: "rsync",
      rsync__exclude: ".git/", rsync__auto: true
    m.vm.network :forwarded_port, guest: 80, host: 8006
  end

  config.vm.define "freebsd10" do |m|
    m.vm.box = "freebsd/FreeBSD-10.2-STABLE"
    m.vm.base_mac = "080027D14C66"
    m.ssh.shell = "sh"
    m.vm.network :forwarded_port, guest: 80, host: 8007
  end
end
