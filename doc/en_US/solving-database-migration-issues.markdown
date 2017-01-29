Solving Database Migration Issues
=================================

- SQL migrations are executed automatically when you upgrade Kanboard to a new version
- For Postgres and Mysql, the current schema version number is stored in the table `schema_version` and for Sqlite this is stored in the variable `user_version
- Migrations are defined in the file `app/Schema/<DatabaseType>.php`
- Each function is a migration
- Each migration is executed in a transaction
- If migration generate an error, a rollback is performed

When upgrading:

- Always backup your data
- Do not run migrations in parallel from multiple processes

If you got the error "Unable to run SQL migrations [...]", here are the steps to fix it manually:

1. Open the file corresponding to your database `app/Schema/Sqlite.php` or `app/Schema/Mysql.php`
2. Go to the failed migration function
3. Execute manually the SQL queries defined in the function
4. If you encounter an error, report the issue to the bug tracker with the exact SQL error
5. When all SQL statements of the migration are executed, update the schema version number
6. Run other migrations
