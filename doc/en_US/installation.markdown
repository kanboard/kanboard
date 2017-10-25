Installation
============

First, check the [requirements](requirements.markdown) before going further.

From the archive (stable version)
---------------------------------

1. You must have a web server with PHP installed
2. Download the source code and copy the directory `kanboard` where you want
3. Check if the directory `data` is writeable by the web server user
4. With your browser go to <http://yourpersonalserver/kanboard>
5. The default login and password is **admin/admin**
6. Start using the software
7. Don't forget to change your password!

The `data` folder is used to store:

- Sqlite database: `db.sqlite`
- Debug file: `debug.log` (if debug mode is enabled)
- Uploaded files: `files/*`
- Image thumbnails: `files/thumbnails/*`

People who are using a remote database (Mysql/Postgresql) and a remote object storage (Aws S3 or similar) don't necessarily need to have a persistent local data folder or to change the permissions for the folder.

From the git repository (development version)
---------------------------------------------

1. `git clone https://github.com/kanboard/kanboard.git`
2. Go to the third step just above

Note: This method will install the **current development version**, use at your own risk.

Installation outside of the document root
-----------------------------------------

If you would like to install Kanboard outside of the web server document root, you need to create at least these symlinks:

```bash
.
├── assets -> ../kanboard/assets
├── cli -> ../kanboard/cli
├── doc -> ../kanboard/doc
├── favicon.ico -> ../kanboard/favicon.ico
├── index.php -> ../kanboard/index.php
├── jsonrpc.php -> ../kanboard/jsonrpc.php
└── robots.txt -> ../kanboard/robots.txt
```

The `.htaccess` is optional because its content can be included directly in the Apache configuration.

You can also define a custom location for the plugins and files folders by changing the [config file](config.markdown).


Other Database Types
--------------------

Kanboard supports Mysql and Postgres as alternative to Sqlite.

- [Mysql configuration](mysql-configuration.markdown)
- [Postgres configuration](postgresql-configuration.markdown)

Optional Installation
---------------------

- Some features of Kanboard require that you run [a daily background job](cronjob.markdown) (Reports and analytics)
- [Install the background worker](worker.markdown) to improve performance

Security
--------

- Don't forget to change the default user/password
- Don't allow everybody to access to the directory `data` from the URL. A `.htaccess` file for Apache and a `web.config` file for IIS is included but other web servers will have to be configured manually.
