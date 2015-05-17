Project settings
================

Go to the menu **Settings**, then choose **Project settings** on the left.

![Project settings](http://kanboard.net/screenshots/documentation/project-settings.png)

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
