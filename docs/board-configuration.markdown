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

### Default categories for new projects

Categories are not global to the application but attached to a project.
Each project can have different categories.

However, if you always create the same categories for all your projects, you can define here the list of categories to create automatically.

### Allow only one subtask in progress at the same time for a user

When this option is enabled, a user can work with only one subtask at the time.

If another subtask have the status "in progress", the user will see this dialog box:

![Subtask user restriction](http://kanboard.net/screenshots/documentation/subtask-user-restriction.png)

### Enable time tracking for subtasks

When this option is enabled, each time the status of a subtask is changed, the start time and the end time are recorded in the database for the assignee.

- When the status changes to "in progress" then the start time is saved
- When the status changes to "done" then the end time is saved

The time spent for the subtask and the task is also updated.

### Show subtask estimates in the user calendar

When enabled, assigned subtasks with the status "todo" and with a defined estimate value will be displayed on the user calendar.

The user calender is available on the dashboard or from the user profile.