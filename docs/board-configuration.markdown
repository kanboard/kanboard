Board settings
===============

Some parameters for boards can be changed on the settings page.
Only administrators can change those settings.

Go to the menu **Settings**, then choose **Board settings** on the left.

![Board settings](http://kanboard.net/screenshots/documentation/board-settings.png)

### Task highlighting

This feature display a shadow around the task when a task is moved recently.

Set the value 0 to disable this feature, 2 days by default (172800 seconds).

Everything moved since 2 days will have shadow around the task.

### Refresh interval for public board

When you share a board, the page will refresh automatically every 60 seconds by default.

### Refresh interval for private board

When your web browser is open on a board, Kanboard check every 10 seconds if something have been changed by someone else.

Technically this process is done by Ajax polling.

### Default columns for new projects

You can change the default column names here.
It's useful if you always create projects with the same columns.

Each column name must be separated by a comma.

By default, Kanboard use those column names: Backlog, Ready, Work in progress and Done.
