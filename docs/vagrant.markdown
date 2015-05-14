Run Kanboard with Vagrant
=========================

Vagrant is used to test Kanboard in different environments.

Several configurations are available:

- Ubuntu 14.04 LTS with Sqlite
- Ubuntu 14.04 LTS with Mysql
- Ubuntu 14.04 LTS with Postgresql
- Debian 7.6 with Sqlite
- Debian 6 with Sqlite
- Centos 7 with Sqlite
- Centos 6.5 with Sqlite
- Freebsd 10 with Sqlite

The installation process is not fully automated for all VM, manual configuration can be required.

To use those configurations, you have to install the **last version of Virtualbox and Vagrant**.

Standard boxes can be downloaded from Vagrant:

```bash
vagrant box add ubuntu/trusty64
vagrant box add chef/debian-7.6
vagrant box add chef/debian-6.0.10
vagrant box add chef/centos-7.0
vagrant box add chef/centos-6.5
vagrant box add chef/freebsd-10.0
```

### Example with Ubuntu and Sqlite

If you want to test Kanboard on Ubuntu with Sqlite:

```bash
vagrant up sqlite
```

Run composer:

```bash
vagrant ssh sqlite
cd /var/www/html          # change the path according to the chosen distribution
sudo composer install
```

After the initialization, go to **http://localhost:8001/**.

If you want to use Postgresql or Mysql, you have to configure Kanboard manually (`config.php`) and configure the database inside the virtual machine.

Available boxes are:

- `vagrant up sqlite`
- `vagrant up mysql`
- `vagrant up postgres`
- `vagrant up debian7`
- `vagrant up debian6`
- `vagrant up centos7`
- `vagrant up centos65`
- `vagrant up freebsd10`
