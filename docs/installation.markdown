Installation
============

Requirements
------------

- Apache or Nginx
- PHP >= 5.3.7
- PHP extensions required: mbstring and pdo_sqlite (don't forget to enable extensions)
- A modern web browser

From the archive (stable version)
---------------------------------

1. You must have a web server with PHP installed
2. Download the source code and copy the directory `kanboard` where you want
3. Check if the directory `data` is writeable
4. With your browser go to <http://yourpersonalserver/kanboard>
5. The default login and password is **admin/admin**
6. Start to use the software
7. Don't forget to change your password!

Note: The folder data is the location where Kanboard stores uploaded files as well as the Sqlite database.

From the repository (development version)
-----------------------------------------

You must install [composer](https://getcomposer.org/) to use this method.

1. `git clone https://github.com/fguillot/kanboard.git`
2. `composer install`
3. Go to the third step just above

Note: This method will install the **current development version**, use at your own risk.

Security
--------

- Don't forget to change the default user/password
- Don't allow everybody to access to the directory `data` from the URL. There is already a `.htaccess` for Apache but nothing for Nginx.
