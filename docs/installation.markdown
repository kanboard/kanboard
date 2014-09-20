Installation
============

Requirements
------------

- Apache or Nginx
- PHP >= 5.3.3
- PHP extensions required: mbstring and pdo_sqlite (don't forget to enable extensions)
- A web browser with HTML5 drag and drop support

From the archive
----------------

1. You must have a web server with PHP installed
2. Download the source code and copy the directory `kanboard` where you want
3. Check if the directory `data` is writeable (It's the location Kanboard stores uploaded files as well as the sqlite database)
4. With your browser go to <http://yourpersonalserver/kanboard>
5. The default login and password is **admin/admin**
6. Start to use the software
7. Don't forget to change your password!

From the repository
-------------------

1. `git clone https://github.com/fguillot/kanboard.git`
2. Go to the third step just above

Security
--------

- Don't forget to change the default user/password
- Don't allow everybody to access to the directory `data` from the URL. There is already a `.htaccess` for Apache but nothing for Nginx.
