Centos Installation
===================

Centos 6.5
----------

Install PHP:

```bash
yum install -y php php-mbstring php-pdo unzip
```

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
wget http://kanboard.net/kanboard-VERSION.zip
unzip kanboard-VERSION.zip
chown -R apache:apache kanboard/data
rm kanboard-VERSION.zip
```

Go to `http://your_server/kanboard/`.