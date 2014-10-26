Centos Installation
===================

Centos 7
--------

Install PHP and Apache:

```bash
yum install -y php php-mbstring php-pdo unzip wget
```

By default Centos 7 use PHP 5.4.16 and Apache 2.4.6.

Restart Apache:

```bash
systemctl restart httpd.service
```

Install Kanboard:

```bash
cd /var/www/html
wget http://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R apache:apache kanboard/data
rm kanboard-latest.zip
```

If SeLinux is enabled, be sure that the Apache user can write to the directory data:

```bash
chcon -R -t httpd_sys_content_rw_t /var/www/html/kanboard/data
```

You can also disable SeLinux if you don't need it.

Centos 6.5
----------

Install PHP and Apache:

```bash
yum install -y php php-mbstring php-pdo unzip wget
```

By default Centos 6.5 use PHP 5.3.3 and Apache 2.2.15.

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
wget http://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R apache:apache kanboard/data
rm kanboard-latest.zip
```

Go to `http://your_server/kanboard/`.