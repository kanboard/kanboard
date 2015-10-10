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
Kanboard version master

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  help                               Displays help for a command
  list                               Lists commands
 export
  export:daily-project-column-stats  Daily project column stats CSV export (number of tasks per column and per day)
  export:subtasks                    Subtasks CSV export
  export:tasks                       Tasks CSV export
  export:transitions                 Task transitions CSV export
 locale
  locale:compare                     Compare application translations with the fr_FR locale
  locale:sync                        Synchronize all translations based on the fr_FR locale
 notification
  notification:overdue-tasks         Send notifications for overdue tasks
 projects
  projects:daily-stats               Calculate daily statistics for all projects
```

Available commands
------------------

### Tasks CSV export

Usage:

```bash
./kanboard export:tasks <project_id> <start_date> <end_date>
```

Example:

```bash
./kanboard export:tasks 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv
```

CSV data are sent to `stdout`.

### Subtasks CSV export

Usage:

```bash
./kanboard export:subtasks <project_id> <start_date> <end_date>
```

Example:

```bash
./kanboard export:subtasks 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv
```

### Task transitions CSV export

Usage:

```bash
./kanboard export:transitions <project_id> <start_date> <end_date>
```

Example:

```bash
./kanboard export:transitions 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv
```

### Export daily summaries data in CSV

The exported data will be printed on the standard output:

```bash
./kanboard export:daily-project-column-stats <project_id> <start_date> <end_date>
```

Example:

```bash
./kanboard export:daily-project-column-stats 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv
```

### Send notifications for overdue tasks

Emails will be sent to all users with notifications enabled.

```bash
./kanboard notification:overdue-tasks
```

You can also display the overdue tasks with the flag `--show`:

```bash
$ ./kanboard notification:overdue-tasks --show
+-----+---------+------------+------------+--------------+----------+
| Id  | Title   | Due date   | Project Id | Project name | Assignee |
+-----+---------+------------+------------+--------------+----------+
| 201 | Test    | 2014-10-26 | 1          | Project #0   | admin    |
| 202 | My task | 2014-10-28 | 1          | Project #0   |          |
+-----+---------+------------+------------+--------------+----------+
```

Cronjob example:

```bash
# Everyday at 8am we check for due tasks
0 8 * * *  cd /path/to/kanboard && ./kanboard notification:overdue-tasks >/dev/null 2>&1
```

### Run daily project stats calculation

You can add a background task to calculate the project statistics everyday:

```bash
$ ./kanboard projects:daily-stats
Run calculation for Project #0
Run calculation for Project #1
Run calculation for Project #10
```
