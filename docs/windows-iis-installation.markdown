How to install Kanboard on Windows Server?
==========================================

Windows 2008/2012 with IIS
---------------------------

### PHP installation

- Install IIS on your server (Add new role and don't forget to enable CGI/FastCGI)
- Install PHP by following the official documentation:
    - [Microsoft IIS 5.1 and IIS 6.0](http://php.net/manual/en/install.windows.iis6.php)
    - [Microsoft IIS 7.0 and later](http://php.net/manual/en/install.windows.iis7.php)
    - [PHP for Windows is available here](http://windows.php.net/download/)

After the installation check if PHP runs correctly:

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
- Don't forget to enable the required php extensions: `pdo_sqlite` and `mbstring`
- If you got an error about "the library MSVCP110.dll is missing", you probably need to download the Visual C++ Redistributable for Visual Studio from the Microsoft website.

### Kanboard installation

- Download the zip file
- Uncompress the archive in `C:\inetpub\wwwroot\kanboard` by example
- Make sure the directory `data` is writable by the IIS user
- You are done, open your web browser to use Kanboard

### Tested configuration

- Windows 2008 R2 Standard Edition / IIS 7.5 / PHP 5.5.16
- Windows 2012 Standard Edition / IIS 8.5 / PHP 5.3.29
