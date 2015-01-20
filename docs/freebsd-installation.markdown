FreeBSD 10 Installation
=======================

Manual installation
-------------------

```bash
$ pkg update
$ pkg upgrade

# Install PHP with some standard extensions
$ pkg install wget unzip mod_php55 \
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
$ echo apache24_enable="YES" >> /etc/rc.conf
```

Set up PHP for Apache:

```bash
$ echo "AddType application/x-httpd-php .php" >> /usr/local/etc/apache24/Includes/php.conf
$ echo "DirectoryIndex index.php index.html" >> /usr/local/etc/apache24/Includes/php.conf
```

Then start Apache:

```bash
$ service apache24 start
```

Install Kanboard:

```bash
$ cd /usr/local/www
$ wget http://kanboard.net/kanboard-latest.zip
$ unzip kanboard-latest.zip
$ chown -R www:www kanboard/data
$ rm kanboard-latest.zip
```
Go to http://your.server.domain.tld/kanboard and enjoy!

Installing from ports
---------------------

Generally 3 elements have to be installed:

- Apache
- mod_php for Apache
- Kanboard

Fetch and extract ports...

```bash
$ portsnap fetch 
$ portsnap extract
```

or update already existing:

```bash
$ portsnap fetch
$ portsnap update
```

More details regarding portsnap can be found in the [FreeBSD Handbook](https://www.freebsd.org/doc/handbook/ports-using.html).

Install Apache:

```bash
$ cd /usr/ports/www/apache24
$ make install clean
```
Enable Apache in your `/etc/rc.conf`:

```bash
$ echo apache24_enable="YES" >> /etc/rc.conf
```

Install mod_php for Apache:

```bash
$ cd /usr/ports/www/mod_php5
$ make install clean
```

Download and extract the latest version of kanboard port:

```bash
$ wget https://bitbucket.org/if0/freebsd-kanboard/get/tip.zip
$ unzip tip.zip
$ cd if0-freebsd-kanboard-*/kanboard
```

Choose proper type of the database (MySQL, Postgresql, SQLite), build port and install:

```bash
$ make config
$ make install clean
$ cd /usr/local/www/kanboard
$ chown -R www:www data
```

Set up PHP for Apache:

```bash
$ echo "AddType application/x-httpd-php .php" >> /usr/local/etc/apache24/Includes/php.conf
$ echo "DirectoryIndex index.php index.html" >> /usr/local/etc/apache24/Includes/php.conf
```

Then start Apache:

```bash
$ service apache24 start
```

Go to http://your.server.domain.tld/kanboard and enjoy!

Please note!
------------

FreeBSD port of kanboard is not yet a part of official FreeBSD ports tree.
It has been commited for aprooval to be included in the repository. Details
regarding the progress can be found [here](https://bugs.freebsd.org/bugzilla/show_bug.cgi?id=196810).

Port is being hosted on [bitbucket](https://bitbucket.org/if0/freebsd-kanboard/). Please feel free to comment,
fork and suggest updates!

PS.
Once kanboard port is part of the FreeBSD ports tree the installation of kanboard will be even easier, something like:

```bash
$ pkg update
$ pkg upgrade
$ pkg install apache24 mod_php5 kanboard
```

Enable Apache in your `/etc/rc.conf`:

```bash
$ echo apache24_enable="YES" >> /etc/rc.conf
```

Set up PHP for Apache:

```bash
$ echo "AddType application/x-httpd-php .php" >> /usr/local/etc/apache24/Includes/php.conf
$ echo "DirectoryIndex index.php index.html" >> /usr/local/etc/apache24/Includes/php.conf
```

Then start Apache:

```bash
$ service apache24 start
```

Go to http://your.server.domain.tld/kanboard and enjoy!
