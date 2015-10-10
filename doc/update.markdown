Update
======

**Always make a backup of your database before upgrading!**

From the archive (stable version)
---------------------------------

1. Close all sessions (logout)
2. Rename your actual Kanboard directory (to keep a backup)
3. Uncompress the new archive and copy your `data` directory to the newly uncompressed directory.
4. Copy your custom `config.php` (if you created one) to the root of the newly uncompressed directory.
5. Make the directory `data` writeable by the web server user
6. Login and check if everything is ok
7. Remove the old Kanboard directory


From the repository (development version)
-----------------------------------------

1. Close all sessions
2. `git pull`
3. `composer install`
3. Login and check if everything is ok

Note: This method will install the **current development version**, use at your own risk.
