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
The procedure is similar for other systems.

Edit the crontab of your web server user:

```bash
sudo crontab -u www-data -e
```

Example to execute the daily cronjob at 8am:

```bash
0 8 * * * cd /path/to/kanboard && ./cli cronjob >/dev/null 2>&1
```

Note: the cronjob process must have write access to the database in case you are using Sqlite.
Usually, running the cronjob under the web server user is enough.

Configuration on Microsoft Windows Server
-----------------------------------------

Before to configure the recurring task, create a batch file (*.bat or *.cmd) that run the Kanboard CLI script.

Here an example (`C:\kanboard.bat`):

```
"C:\php\php.exe" -f "C:\inetpub\wwwroot\kanboard\cli" cronjob
```

**You must change the path of the PHP executable and the path of the Kanboard's script according to your installation.**

Configure the Windows Task Scheduler:

1. Go to "Administrative Tools"
2. Open the "Task Scheduler"
3. On the right, choose "Create Task"
4. Choose a name, for example you can use "Kanboard"
5. Under "Security Options", choose a user that can write to the database in case you are using Sqlite (might be IIS_IUSRS depending of your configuration)
6. Create a new "Trigger", choose daily and a time, midnight for example
7. Add a new action, choose "Start a program" and select the batch file created above
