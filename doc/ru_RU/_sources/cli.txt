Command Line Interface
======================

Kanboard provides a simple command line interface that can be used from
any Unix terminal. This tool can be used only on the local machine.

This feature is useful to run commands outside of the web server
processes.

Usage
-----

-  Open a terminal and go to your Kanboard directory (example:
   ``cd /var/www/kanboard``)
-  Run the command ``./kanboard``

.. code:: bash

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
      cronjob                            Execute daily cronjob
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
     plugin
      plugin:install                     Install a plugin from a remote Zip archive
      plugin:uninstall                   Remove a plugin
      plugin:upgrade                     Update all installed plugins
     projects
      projects:daily-stats               Calculate daily statistics for all projects
     trigger
      trigger:tasks                      Trigger scheduler event for all tasks
     user
      user:reset-2fa                     Remove two-factor authentication for a user
      user:reset-password                Change user password

Available commands
------------------

Tasks CSV export
~~~~~~~~~~~~~~~~

Usage:

.. code:: bash

    ./kanboard export:tasks <project_id> <start_date> <end_date>

Example:

.. code:: bash

    ./kanboard export:tasks 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv

CSV data are sent to ``stdout``.

Subtasks CSV export
~~~~~~~~~~~~~~~~~~~

Usage:

.. code:: bash

    ./kanboard export:subtasks <project_id> <start_date> <end_date>

Example:

.. code:: bash

    ./kanboard export:subtasks 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv

Task transitions CSV export
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Usage:

.. code:: bash

    ./kanboard export:transitions <project_id> <start_date> <end_date>

Example:

.. code:: bash

    ./kanboard export:transitions 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv

Export daily summaries data in CSV
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The exported data will be printed on the standard output:

.. code:: bash

    ./kanboard export:daily-project-column-stats <project_id> <start_date> <end_date>

Example:

.. code:: bash

    ./kanboard export:daily-project-column-stats 1 2014-10-01 2014-11-30 > /tmp/my_custom_export.csv

Send notifications for overdue tasks
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Emails will be sent to all users with notifications enabled.

.. code:: bash

    ./kanboard notification:overdue-tasks

Optional parameters:

-  ``--show``: Display notifications sent
-  ``--group``: Group all overdue tasks for one user (from all projects)
   in one email
-  ``--manager``: Send all overdue tasks to project manager(s) in one
   email

You can also display the overdue tasks with the flag ``--show``:

.. code:: bash

    ./kanboard notification:overdue-tasks --show
    +-----+---------+------------+------------+--------------+----------+
    | Id  | Title   | Due date   | Project Id | Project name | Assignee |
    +-----+---------+------------+------------+--------------+----------+
    | 201 | Test    | 2014-10-26 | 1          | Project #0   | admin    |
    | 202 | My task | 2014-10-28 | 1          | Project #0   |          |
    +-----+---------+------------+------------+--------------+----------+

Run daily project stats calculation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This command calculate the statistics of each project:

.. code:: bash

    ./kanboard projects:daily-stats
    Run calculation for Project #0
    Run calculation for Project #1
    Run calculation for Project #10

Trigger for tasks
~~~~~~~~~~~~~~~~~

This command send a "daily cronjob event" to all open tasks of each
project.

.. code:: bash

    ./kanboard trigger:tasks
    Trigger task event: project_id=2, nb_tasks=1

Reset user password
~~~~~~~~~~~~~~~~~~~

.. code:: bash

    ./kanboard user:reset-password my_user

You will be prompted for a password and confirmation. Characters are not
printed to the screen.

Remove two-factor authentication for a user
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code:: bash

    ./kanboard user:reset-2fa my_user

Install a plugin
~~~~~~~~~~~~~~~~

.. code:: bash

    ./kanboard plugin:install https://github.com/kanboard/plugin-github-auth/releases/download/v1.0.1/GithubAuth-1.0.1.zip

Note: Installed files will have the same permissions as the current user

Remove a plugin
~~~~~~~~~~~~~~~

.. code:: bash

    ./kanboard plugin:uninstall Budget

Upgrade all plugins
~~~~~~~~~~~~~~~~~~~

.. code:: bash

    ./kanboard plugin:upgrade
    * Updating plugin: Budget Planning
    * Plugin up to date: Github Authentication

