Frequently Asked Questions
==========================

Can you recommend a web hosting provider for Kanboard?
------------------------------------------------------

Kanboard works well with any great VPS hosting provider such as [Digital Ocean](https://www.digitalocean.com/?refcode=4b541f47aae4),
[Linode](https://www.linode.com/?r=4e381ac8a61116f40c60dc7438acc719610d8b11) or [Gandi](https://www.gandi.net/).

To have the best performances, choose a provider with fast disk I/O because Kanboard use Sqlite by default.
Avoid hosting providers that use a shared NFS mount point.

I get a blank page after installing or upgrading Kanboard
---------------------------------------------------------

- Check if you have installed all requirements on your server
- Check if the files have the correct permissions
- If you use php-fpm and opcode caching, reload the process to be sure to clear the cache
- Enable PHP error logging in your php.ini
- Check the PHP and Apache error logs you should see the exact error


Page not found and the url seems wrong (&amp;amp;)
----------------------------------------------

- The url looks like `/?controller=auth&amp;action=login&amp;redirect_query=` instead of `?controller=auth&action=login&redirect_query=`
- Kanboard returns a "Page not found" error

This issue come from your PHP configuration, the value of `arg_separator.output` is not the PHP's default, there is different ways to fix that:

Change the value directly in your `php.ini` if you have the permission:

```
arg_separator.output = "&"
```

Override the value with a `.htaccess`:

```
php_value arg_separator.output "&"
```

Otherwise Kanboard will try to override the value directly in PHP.


Known issues with eAccelerator
------------------------------

Kanboard doesn't work very well with [eAccelerator](http://eaccelerator.net).
The issue caused can be a blank page or an Apache crash:

```
[Wed Mar 05 21:36:56 2014] [notice] child pid 22630 exit signal Segmentation fault (11)
```

The best way to avoid this issue is to disable eAccelerator or define manually which files you want to cache with the config parameter `eaccelerator.filter`.

The project [eAccelerator seems dead and not updated since 2012](https://github.com/eaccelerator/eaccelerator/commits/master).
We recommend to switch to the last version of PHP because it's bundled with [OPcache](http://php.net/manual/en/intro.opcache.php).


Why the minimum requirement is PHP 5.3.3?
-----------------------------------------

Kanboard use the function `password_hash()` to crypt passwords but it's available only for PHP >= 5.5.

However, there is a backport for [older versions of PHP](https://github.com/ircmaxell/password_compat#requirements).
This library require at least PHP 5.3.7 to work correctly.

Apparently, Centos and Debian backports security patches so PHP 5.3.3 should be ok.

Kanboard v1.0.10 and v1.0.11 requires at least PHP 5.3.7 but this change has been reverted to be compatible with PHP 5.3.3 with Kanboard >= v1.0.12


How to test Kanboard with the PHP built-in web server?
------------------------------------------------------

If you don't want to install a web server like Apache on localhost. You can test with the [embedded web server of PHP](http://www.php.net/manual/en/features.commandline.webserver.php):

```bash
unzip kanboard-VERSION.zip
cd kanboard
php -S localhost:8000
open http://localhost:8000/
```


How to migrate my tasks from Wunderlist?
----------------------------------------

You can use an external tool to import automatically your tasks and lists from Wunderlist to Kanboard.

This is a command line script made by a contributor of Kanboard.
It's simple, quick and dirty but it works :)

More information here:

- [Wunderlist](http://www.wunderlist.com/)
- <https://github.com/EpocDotFr/WunderlistToKanboard>


How to install Kanboard on Yunohost?
------------------------------------

[YunoHost](https://yunohost.org/) is a server operating system aiming to make self-hosting accessible to everyone.

There is a [package to install Kanboard on Yunohost easily](https://github.com/mbugeia/kanboard_ynh).


Are there some tutorials about Kanboard in other languages?
-----------------------------------------------------------

- [German article series about Kanboard](http://demaya.de/wp/2014/07/kanboard-eine-jira-alternative-im-detail-installation/)
