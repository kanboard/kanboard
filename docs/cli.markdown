Command Line Interface
======================

Kanboard provide a simple command line interface that can be used from any Unix terminal.

This feature is useful to run commands outside the web server by example a huge report.

Actually there is only one command, more stuff will be added later.

Usage
-----

- Open a terminal and go to your Kanboard directory (example: `cd /var/www/kanboard`)
- Run the command `./kanboard`

```bash
$ ./kanboard
Kanboard command line interface
===============================

- Task export to stdout (CSV format): ./kanboard export-csv <project_id> <start_date> <end_date>
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
