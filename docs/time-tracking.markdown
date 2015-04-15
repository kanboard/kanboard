Time Tracking
=============

Time tracking information can be defined at the task level or at the subtask level.

Task time tracking
------------------

![Task time tracking](http://kanboard.net/screenshots/documentation/task-time-tracking.png)

Tasks have two fields:

- Time estimated
- Time spent

These values represents hours of work and have to be set manually.

Subtask time tracking
---------------------

![Subtask time tracking](http://kanboard.net/screenshots/documentation/subtask-time-tracking.png)

Subtasks also have the fields "time spent" and "time estimated".
However, when you set a value for those fields, **the task time tracking values becomes the sum of all subtask values**.

User time tracking
------------------

In the board settings, you can enable subtasks time tracking for users.

Each time a subtask status change, the start time and the end time are saved in a seperate table automatically.
The time spent is automatically calculated for tasks and subtasks when the subtask is completed.

- Changing subtask status from "todo" to "in pogress" logs the start time
- Changing subtask status from "in progress" to "done" logs the end time but also update the time spent of the subtask and the task

The breakdown by user is also visible in the tasks details:
I