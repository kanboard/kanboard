FreeBSD 10 Installation
=======================

Install from packages
---------------------

```bash
$ pkg update
$ pkg upgrade
$ pkg install apache24 mod_php56 kanboard
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

Add symlink to Kanboard folder into your Apache docroot:

```bash
cd /usr/local/www/apache24/data
ln -s /usr/local/www/kanboard
```

Go to http://your.server.domain.tld/kanboard and enjoy!

*Notes*:
- If you want to use additional features like LDAP integration etc.
please install proper PHP module using pkg.
- You may have to adjust the permissions of the folder data

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

Install Kanboard form ports:

```bash
$ cd /usr/ports/www/kanboard
$ make install clean
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

*Note*:
If you want to use additional features like LDAP integration etc.
please install proper PHP module from `lang/php5-extensions`.

Manual installation
-------------------

As of version 1.0.16 Kanboard can be found in FreeBSD ports
there is no need to install it manually.

Please note
-----------

- Port is being hosted on [bitbucket](https://bitbucket.org/if0/freebsd-kanboard/). Feel free to comment,
fork and suggest updates!
- Some features of Kanboard require that you run [a daily background job](cronjob.markdown).

