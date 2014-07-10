# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

$script = <<SCRIPT
# install packages
apt-get update
apt-get install -y apache2 php5 php5-sqlite php5-ldap php5-xdebug
service apache2 restart
rm -f /var/www/html/index.html
date > /etc/vagrant_provisioned_at
echo "Go to http://localhost:8080/ (admin/admin) !"
SCRIPT

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # Image
  config.vm.box = "ubuntu/trusty64"
  config.vm.box_url = "http://cloud-images.ubuntu.com/vagrant/trusty/current/trusty-server-cloudimg-amd64-vagrant-disk1.box"

  # Network
  config.vm.network :forwarded_port, guest: 80, host: 8080
  #config.vm.network "public_network", :bridge => "en0: Wi-Fi (AirPort)"

  # Setup
  config.vm.provision "shell", inline: $script
  config.vm.synced_folder ".", "/var/www/html", owner: "www-data", group: "www-data"
end
