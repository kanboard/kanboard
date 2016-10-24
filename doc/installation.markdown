Installation
============

Firstly, check the [requirements](requirements.markdown) before to go further.

From the archive (stable version)
---------------------------------

1. You must have a web server with PHP installed
2. Download the source code and copy the directory `kanboard` where you want
3. Check if the directory `data` is writeable by the web server user
4. With your browser go to <http://yourpersonalserver/kanboard>
5. The default login and password is **admin/admin**
6. Start to use the software
7. Don't forget to change your password!

The `data` folder is used to store:

- Sqlite database: `db.sqlite`
- Debug file: `debug.log` (if debug mode enabled)
- Uploaded files: `files/*`
- Image thumbnails: `files/thumbnails/*`

People who are using a remote database (Mysql/Postgresql) and a remote file storage (Aws S3 or similar) don't necessarily need to have a persistent local data folder or to change the permission.

From the git repository (development version)
---------------------------------------------

You must install [composer](https://getcomposer.org/) to use this method.

1. `git clone https://github.com/kanboard/kanboard.git`
2. `composer install --no-dev`
3. Go to the third step just above

Note: This method will install the **current development version**, use at your own risk.

Installation outside of the document root
-----------------------------------------

If you would like to install Kanboard outside of the web server document root, you need to create at least these symlinks:

```bash
.
├── assets -> ../kanboard/assets
├── doc -> ../kanboard/doc
├── favicon.ico -> ../kanboard/favicon.ico
├── index.php -> ../kanboard/index.php
├── jsonrpc.php -> ../kanboard/jsonrpc.php
└── robots.txt -> ../kanboard/robots.txt
```

The `.htaccess` is optional because its content can be included directly in the Apache configuration.

You can also define a custom location for the plugins and files folders by changing the [config file](config.markdown).

Optional installation
---------------------

- Some features of Kanboard require that you run [a daily background job](cronjob.markdown) (Reports and analytics)
- [Install the background worker](worker.markdown) to improve the performances

Security
--------

- Don't forget to change the default user/password
- Don't allow everybody to access to the directory `data` from the URL. There is already a `.htaccess` for Apache and a `web.config` file for IIS but nothing for other web servers.
