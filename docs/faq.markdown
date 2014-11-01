Frequently Asked Questions
==========================

Can you recommend a web hosting provider for Kanboard?
------------------------------------------------------

Kanboard works well with any great VPS hosting provider such as [Digital Ocean](https://www.digitalocean.com/?refcode=4b541f47aae4),
[Linode](https://www.linode.com) or [Gandi](https://www.gandi.net/).

To have the best performances, choose a provider with fast disk I/O because Kanboard use Sqlite by default.
Avoid hosting providers that use a shared NFS mount point.


Which web browsers are supported?
---------------------------------

Kanboard have been tested on the following devices:

### Desktop

- Mozilla Firefox
- Safari
- Google Chrome
- Internet Explorer > 10

### Tablets

- iPad (iOS)
- Nexus 7 (Android)


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


Why the minimum requirement is PHP 5.3.3 or 5.3.7?
--------------------------------------------------

Kanboard use the function `password_hash()` to crypt passwords but it's available only for PHP >= 5.5.

However, there is a backport for [older versions of PHP](https://github.com/ircmaxell/password_compat#requirements).
This library needs to have at least PHP 5.3.7 to work correctly (with Debian Wheezy, using PHP 5.3.3 should be fine).


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
