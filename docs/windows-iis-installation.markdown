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

Edit the `php.ini`, uncomment these PHP modules:

```ini
extension=php_curl.dll
extension=php_gd2.dll
extension=php_ldap.dll
extension=php_mbstring.dll
extension=php_openssl.dll
extension=php_pdo_sqlite.dll
```

Set the timezone:

```ini
date.timezone = America/Montreal
```

The list of supported timezones can be found in the [PHP documentation](http://php.net/manual/en/timezones.america.php).

Check if PHP runs correctly:

Go the IIS document root `C:\inetpub\wwwroot` and create a file `phpinfo.php`:

```php
<?php

phpinfo();

?>
```

Open a browser at `http://localhost/phpinfo.php` and you should see the current PHP settings.
If you got an error 500, something is not correctly done in your installation.

Notes:

- If you use PHP < 5.4, you have to enable the short tags in your php.ini
- Don't forget to enable the required php extensions mentioned above
- If you got an error about "the library MSVCP110.dll is missing", you probably need to download the Visual C++ Redistributable for Visual Studio from the Microsoft website.

Kanboard installation
---------------------

- Download the zip file
- Uncompress the archive in `C:\inetpub\wwwroot\kanboard` by example
- Make sure the directory `data` is writable by the IIS user
- Open your web browser to use Kanboard http://localhost/kanboard/
- The default credentials are **admin/admin**

Tested configurations
---------------------

- Windows 2008 R2 Standard Edition / IIS 7.5 / PHP 5.5.16
- Windows 2012 Standard Edition / IIS 8.5 / PHP 5.3.29
