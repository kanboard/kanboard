Run Kanboard with Vagrant
=========================

Vagrant is used to test Kanboard in different environments.
Several configurations are available:

- Ubuntu 14.04 LTS with Kanboard/Sqlite
- Ubuntu 14.04 LTS with Kanboard/Mysql
- Ubuntu 14.04 LTS with Kanboard/Postgresql
- Debian 7.6 with Kanboard/Sqlite

To use those configurations, you have to install the **last version** of Virtualbox and Vagrant.

Standard boxes can be download from [VagrantCloud](https://vagrantcloud.com):

```bash
vagrant box add ubuntu/trusty64
vagrant box add chef/debian-7.6
```

If you want to test Kanboard on Ubuntu with Sqlite:

```bash
vagrant up sqlite
```

After the initialization, go to http://localhost:8001/.

To test with Mysql:

```bash
vagrant up mysql
```

You have to configure Kanboard to use Mysql or Postgresql the first time (config file and database access).

Available boxes are:

- `vagrant up sqlite`
- `vagrant up mysql`
- `vagrant up postgres`
- `vagrant up debian7`
