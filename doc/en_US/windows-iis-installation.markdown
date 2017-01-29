Installation on Windows 2008/2012 with IIS
==========================================

This guide will help you to setup step by step Kanboard on a Windows Server with IIS and PHP.

PHP installation
----------------

- Install IIS on your server (Add a new role and don't forget to enable CGI/FastCGI)
- Install PHP by following the official documentation:
    - [Microsoft IIS 5.1 and IIS 6.0](http://php.net/manual/en/install.windows.iis6.php)
    - [Microsoft IIS 7.0 and later](http://php.net/manual/en/install.windows.iis7.php)
    - [PHP for Windows is available here](http://windows.php.net/download/)


### PHP.ini

You need at least, these extensions in your `php.ini`:

```ini
extension=php_gd2.dll
extension=php_ldap.dll
extension=php_mbstring.dll
extension=php_openssl.dll
extension=php_pdo_sqlite.dll
```

The complete list of required PHP extensions is available on the [requirements page](requirements.markdown)

Do not forget to set the time zone:

```ini
date.timezone = America/Montreal
```

The list of supported time zones can be found in the [PHP documentation](http://php.net/manual/en/timezones.america.php).

Notes:

- If you use PHP < 5.4, you have to enable the short tags in your php.ini
- Don't forget to enable the required php extensions mentioned above
- If you got an error about "the library MSVCP110.dll is missing", you probably need to download the Visual C++ Redistributable for Visual Studio from the Microsoft website.

IIS Modules
-----------

The Kanboard archive contains a `web.config` file to enable [URL rewriting](nice-urls.markdown). 
This configuration require the [Rewrite module for IIS](http://www.iis.net/learn/extensions/url-rewrite-module/using-the-url-rewrite-module).

If you don't have the rewrite module, you will get an internal server error (500) from IIS.
If you don't want to have Kanboard with nice URLs, you can remove the file `web.config`.

Kanboard installation
---------------------

- Download the zip file
- Decompress the archive in `C:\inetpub\wwwroot\kanboard` by example
- Make sure the directory `data` is writable by the IIS user
- Open your web browser to use Kanboard http://localhost/kanboard/
- The default credentials are **admin/admin**
- [URL rewrite configuration](nice-urls.markdown)

Notes
-----

- Some features of Kanboard require that you run [a daily background job](cronjob.markdown).

