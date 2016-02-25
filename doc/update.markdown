Upgrade Kanboard to a new version
=================================

Upgrading Kanboard to a newer version is seamless.
The process can be summarized to simply copy your data folder to the new Kanboard folder.
Kanboard will run database migrations automatically for you.

Important things to do before updating
--------------------------------------

- Always make a backup of your data before upgrading
- Always read the [change log](https://github.com/fguillot/kanboard/blob/master/ChangeLog) to check for breaking changes
- Always close all user sessions (flush all sessions on the server)

From the archive (stable version)
---------------------------------

1. Decompress the new archive
2. Copy the content of your data folder into the newly uncompressed directory
3. Copy your custom `config.php` if you have one
4. Copy your plugins if necessary
5. Make sure the directory `data` is writeable by your web server user
6. Test
7. Remove your old Kanboard directory

From the repository (development version)
-----------------------------------------

1. `git pull`
2. `composer install`
3. Login and check if everything is ok

Note: This method will install the **current development version**, use at your own risk.
