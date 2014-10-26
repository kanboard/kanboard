Command Line Interface
======================

Kanboard provide a simple command line interface that can be used from any Unix terminal.
This tool can be used only on the local machine.

This feature is useful to run commands outside the web server process by example running a huge report.

Usage
-----

- Open a terminal and go to your Kanboard directory (example: `cd /var/www/kanboard`)
- Run the command `./kanboard`

```bash
$ ./kanboard
Kanboard command line interface
===============================

- Task export to stdout (CSV format): ./kanboard export-csv <project_id> <start_date> <end_date>
- Send notifications for due tasks: ./kanboard send-notifications-due-tasks
```

Available commands
------------------

### CSV export of tasks

Usage:

```bash
./kanboard export-csv <project_id> <start_date> <end_date>
```

Example:

```bash
./kanboard export-csv 1 2014-07-14 2014-07-20 > /tmp/my_custom_export.csv
```

### Send notifications for due tasks

Emails will be sent to all users with notifications enabled.

```bash
./kanboard send-notifications-due-tasks
```

Cronjob example:

```bash
# Everyday at 8am we check for due tasks
0 8 * * *  cd /path/to/kanboard && ./kanboard send-notifications-due-tasks >/dev/null 2>&1
```
