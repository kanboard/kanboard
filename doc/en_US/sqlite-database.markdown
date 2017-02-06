Sqlite database management
==========================

Kanboard uses Sqlite by default to store its data.
All tasks, projects and users are stored inside this database.

Technically, the database is just a single file located inside the directory `data` and named `db.sqlite`.

Export/Backup
-------------

### Command line

Doing a backup is very easy, just copy the file `data/db.sqlite` somewhere else when nobody use the software.

### User interface

You can also download at any time the database directly from the **settings** menu.

The downloaded database is compressed with Gzip, the filename becomes `db.sqlite.gz`.

Import/Restoration
------------------

There is actually no way to restore the database from the user interface.
The restoration must be done manually when no body use the software.

- To restore an old backup, just replace and overwrite the actual file `data/db.sqlite`.
- To uncompress a gzipped database, execute this command from a terminal `gunzip db.sqlite.gz`.

Optimization
------------

Occasionally, it's possible to optimize the database file by running the command `VACUUM`.
This command rebuild the entire database and can be used for several reasons:

- Reduce the file size, deleting data produce empty space but doesn't change the file size.
- The database is fragmented due to frequent inserts or updates.

### From the command line

```
sqlite3 data/db.sqlite 'VACUUM'
```

### From the user interface

Go to the menu **settings** and click on the link **Optimize the database**.

For more information, read the [Sqlite documentation](https://sqlite.org/lang_vacuum.html).
