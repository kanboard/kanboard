Recurring tasks
===============

To fit with the Kanban methodology, the recurring tasks are not based on a date but on board events.

- Recurring tasks are duplicated to the first column of the board when the selected events occurs
- The due date can be recalculated automatically
- Each task records the task id of the parent task that created it and the child task created

Configuration
-------------

Go to the task view page or use the dropdown menu on the board, then select **Edit recurrence**.

![Recurring task](http://kanboard.net/screenshots/documentation/recurring-tasks.png)

There are 3 triggers that currently create a new recurring task:

- Moving a task from the first column
- Moving a task to the last column
- Closing the task

Due dates, if set on the current task, can be recalculated by a given factor of days, months or years.
The base date for the calculation of the new due date can be either the existing due date, or the action date.
