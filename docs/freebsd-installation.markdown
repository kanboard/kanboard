Freebsd Installation
====================

Freebsd 10
----------

```bash
pkg update
pkg upgrade

# Install PHP with some standard extensions
pkg install wget unzip mod_php55 \
    php55-session php55-pdo_sqlite php55-pdo \
    php55-openssl php55-opcache php55-mbstring \
    php55-json php55-curl php55-mcrypt \
    php55-zlib php55-simplexml php55-xml php55-filter \
    php55-iconv php55-dom php55-ctype
```

Check if PHP is correctly installed:

```bash
$ php -v
PHP 5.5.19 (cli) (built: Nov 19 2014 04:37:37)
Copyright (c) 1997-2014 The PHP Group
Zend Engine v2.5.0, Copyright (c) 1998-2014 Zend Technologies
    with Zend OPcache v7.0.4-dev, Copyright (c) 1999-2014, by Zend Technologies
```

Enable Apache in your `/etc/rc.conf`:

```bash
echo apache24_enable="YES" >> /etc/rc.conf
```

Setting up PHP for Apache, just add those lines into `/usr/local/etc/apache24/Includes/php.conf`:

```bash
AddType application/x-httpd-php .php
DirectoryIndex index.php index.html
```

Then start Apache:

```
service apache24 start
```

Install Kanboard:

```bash
wget http://kanboard.net/kanboard-latest.zip
unzip kanboard-latest.zip
chown -R www:www kanboard/data
rm kanboard-latest.zip
```
