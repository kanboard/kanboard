Update
======

**Always make a backup of your database before upgrading!**

From the archive (stable version)
---------------------------------

1. Close your session (logout)
2. Rename your actual Kanboard directory (to keep a backup)
3. Uncompress the new archive and copy your `data` directory to the newly uncompressed directory.
4. Make the directory `data` writeable by the web server user
5. Login and check if everything is ok
6. Remove the old Kanboard directory


From the repository (development version)
-----------------------------------------

1. Close your session (logout)
2. `git pull`
3. `composer install`
3. Login and check if everything is ok

Note: This method will install the **current development version**, use at your own risk.
