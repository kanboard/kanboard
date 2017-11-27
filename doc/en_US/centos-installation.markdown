Kanboard Installation on CentOS
===============================

Centos 7
--------

Install PHP and Apache:

```bash
yum install -y php php-mbstring php-pdo php-gd unzip wget
```

By default, Centos 7 use PHP 5.4.16 and Apache 2.4.6.

Restart Apache:

```bash
systemctl restart httpd.service
```

Install Kanboard:

```bash
cd /var/www/html

# Download the latest release from https://github.com/kanboard/kanboard/releases
wget https://github.com/kanboard/kanboard/archive/v<version>.zip

unzip kanboard-<version>.zip
chown -R apache:apache kanboard-<version>/data
rm kanboard-<version>.zip
```

Centos 6.x
----------

Install PHP and Apache:

```bash
yum install -y php php-mbstring php-pdo php-gd unzip wget
```

By default, Centos 6.5 use PHP 5.3.3 and Apache 2.2.15.

Enable short tags:

- Edit the file `/etc/php.ini`
- Change the line `short_open_tag = On`

Restart Apache:

```bash
service httpd restart
```

Install Kanboard:

```bash
cd /var/www/html

# Download the latest release from https://github.com/kanboard/kanboard/releases
wget https://github.com/kanboard/kanboard/archive/v<version>.zip

unzip kanboard-<version>.zip
chown -R apache:apache kanboard-<version>/data
rm kanboard-<version>.zip
```

SELinux restrictions
--------------------

If SELinux is enabled, be sure that the Apache user can write to the directory data:

```bash
chcon -R -t httpd_sys_content_rw_t /var/www/html/kanboard/data
```

Be sure to configure your server to allow Kanboard to send emails and make external network requests, by example with SELinux:

```bash
setsebool -P httpd_can_network_connect=1
```

Allowing external connections is necessary if you use LDAP, SMTP, Web hooks or any third-party integration.

Notes
-----

Some features of Kanboard require that you run [a daily background job](cronjob.markdown).
