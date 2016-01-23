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
- Check the PHP and Apache error logs
- Check if the files have the correct permission
- If you use an aggressive OPcode caching, reload your web-server or php-fpm


Page not found and the URL seems wrong (&amp;amp;)
--------------------------------------------------

- The URL looks like `/?controller=auth&amp;action=login&amp;redirect_query=` instead of `?controller=auth&action=login&redirect_query=`
- Kanboard returns a "Page not found" error

This issue comes from your PHP configuration, the value of `arg_separator.output` is not the PHP's default, there is different ways to fix that:

Change the value directly in your `php.ini` if you can:

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

Kanboard uses the function `password_hash()` to crypt passwords but it's available only for PHP >= 5.5.

However, there is a back-port for [older versions of PHP](https://github.com/ircmaxell/password_compat#requirements).
This library requires at least PHP 5.3.7 to work correctly.

Apparently, Centos and Debian back-ports security patches so PHP 5.3.3 should be ok.

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


How to install Kanboard on Yunohost?
------------------------------------

[YunoHost](https://yunohost.org/) is a server operating system aiming to make self-hosting accessible to everyone.

There is a [package to install Kanboard on Yunohost easily](https://github.com/mbugeia/kanboard_ynh).


Where can I find a list of related projects?
--------------------------------------------

- [Kanboard API python client by @freekoder](https://github.com/freekoder/kanboard-py)
- [Kanboard Presenter by David Eberlein](https://github.com/davideberlein/kanboard-presenter)
- [CSV2Kanboard by @ashbike](https://github.com/ashbike/csv2kanboard)
- [Kanboard for Yunohost by @mbugeia](https://github.com/mbugeia/kanboard_ynh)
- [Trello import script by @matueranet](https://github.com/matueranet/kanboard-import-trello)
- [Chrome extension by Timo](https://chrome.google.com/webstore/detail/kanboard-quickmenu/akjbeplnnihghabpgcfmfhfmifjljneh?utm_source=chrome-ntp-icon), [Source code](https://github.com/BlueTeck/kanboard_chrome_extension)
- [Python client script by @dzudek](https://gist.github.com/fguillot/84c70d4928eb1e0cb374)


Are there some tutorials about Kanboard in other languages?
-----------------------------------------------------------

- [German article series about Kanboard](http://demaya.de/wp/2014/07/kanboard-eine-jira-alternative-im-detail-installation/)
