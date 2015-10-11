Run Kanboard with Vagrant
=========================

Vagrant is used to test Kanboard in different environments.

Several configurations are available:

- Ubuntu 14.04 LTS
- Debian 8
- Debian 7
- Debian 6
- Centos 7
- Centos 6.7
- Freebsd 10

The installation process is not fully automated for all VM, manual configuration can be required.

To use those configurations, you have to install the **last version of Virtualbox and Vagrant**.

Standard boxes can be downloaded from Vagrant:

```bash
vagrant box add ubuntu/trusty64
vagrant box add debian/jessie64
vagrant box add debian/wheezy64
vagrant box add bento/debian-6.0.10
vagrant box add centos/7
vagrant box add bento/centos-6.7
vagrant box add freebsd/FreeBSD-10.2-STABLE
```

### Example with Ubuntu and Sqlite

If you want to test Kanboard on Ubuntu with Sqlite:

```bash
vagrant up sqlite
```

The current directory is synced to the Apache document root.

Composer dependencies have to be there, so if you didn't run `composer install` on your host machine you can also do it on the guest machine.

Each box have its own TCP port:

- ubuntu: http://localhost:8001/
- debian8: http://localhost:8002/
- debian7: http://localhost:8003/
- debian6: http://localhost:8004/
- centos7: http://localhost:8005/
- centos6: http://localhost:8006/
- freebsd10: http://localhost:8007/

Available boxes are:

- `vagrant up ubuntu`
- `vagrant up debian8`
- `vagrant up debian7`
- `vagrant up debian6`
- `vagrant up centos7`
- `vagrant up centos6`
- `vagrant up freebsd10`

Any specific configuration have to done manually (Postgres or Mysql).
