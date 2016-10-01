Background Job Scheduling
=========================

To work properly, Kanboard requires that a background job run on a daily basis.
Usually on Unix platforms, this process is done by `cron`.

This background job is necessary for these features:

- Reports and analytics (calculate daily stats of each projects)
- Send overdue task notifications
- Execute automatic actions connected to the event "Daily background job for tasks"

Configuration on Unix and Linux platforms
-----------------------------------------

There are multiple ways to define a cronjob on Unix/Linux operating systems, this example is for Ubuntu 14.04.
The procedure is similar to other systems.

Edit the crontab of your web server user:

```bash
sudo crontab -u www-data -e
```

Example to execute the daily cronjob at 8am:

```bash
0 8 * * * cd /path/to/kanboard && ./kanboard cronjob >/dev/null 2>&1
```

Note: the cronjob process must have write access to the database in case you are using Sqlite.
Usually, running the cronjob under the web server user is enough.
