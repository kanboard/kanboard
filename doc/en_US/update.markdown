Upgrade Kanboard to a new version
=================================

Most of the time, upgrading Kanboard to a newer version is seamless.
The process could be summarized to simply copy your data folder to the new Kanboard folder.
Kanboard will run database migrations automatically for you.

Important things to do before updating
--------------------------------------

- **Always make a backup of your data before upgrading**
- **Check that your backup is valid!**
- Check again
- Always read the [change history](https://github.com/kanboard/kanboard/blob/master/ChangeLog) to check for breaking changes
- Stop the worker if you use it
- Put the web server in maintenance mode to avoid people use the software while upgrading

From the archive (stable version)
---------------------------------

1. Decompress the new archive
2. Copy the `data` folder into the newly uncompressed directory
3. Copy your custom `config.php` if you have one
4. If you have installed some plugins, use the latest version
5. Make sure the directory `data` is writeable by your web server user
6. Test
7. Remove your old Kanboard directory

From the repository (development version)
-----------------------------------------

1. `git pull`
2. `composer install --no-dev`
3. Login and check if everything is ok

Note: This method will install the **current development version**, use at your own risk.

Running SQL migrations manually
-------------------------------

By default, SQL migrations are executed automatically. The schema version is checked at each request.
In this way, when you upgrade Kanboard to another version, the database schema is updated for you.

You may want to disable this behaviour is case you have a specific configuration.
For example, if multiple processes try to apply the migrations in the same time you might have some concurrency problems even if each operation is executed inside a transaction.

To disable this feature, set the parameter `DB_RUN_MIGRATIONS` at `false` in your [config file](config.markdown).

When you will have to upgrade Kanboard, run this command:

```bash
./cli db:migrate
```
