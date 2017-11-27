Project Settings
================

Go to the menu **Settings**, then choose **Project settings** on the left.

![Project settings](../screenshots/project-settings.png)

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

![Subtask user restriction](../screenshots/subtask-user-restriction.png)

### Trigger automatically subtask time tracking

- If enabled, when a subtask status is changed to "in progress", the timer will start automatically.
- Disable this option if you don't use time tracking.

### Include closed tasks in the cumulative flow diagram

- If enabled, closed tasks will be included in the cumulative flow diagram.
- If disabled, only open tasks will be included.
- This option affects the column "total" of the table "project_daily_column_stats"

