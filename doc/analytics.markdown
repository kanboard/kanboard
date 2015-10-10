Analytics
=========

Each project have an analytics section. Depending how you are using Kanboard, you can see those reports:

User repartition
----------------

![User repartition](http://kanboard.net/screenshots/documentation/user-repartition.png)

This pie chart show the number of open tasks assigned per user.

Task distribution
-----------------

![Task distribution](http://kanboard.net/screenshots/documentation/task-distribution.png)

This pie chart gives an overview of the number of open tasks per column.

Cumulative flow diagram
-----------------------

![Cumulative flow diagram](http://kanboard.net/screenshots/documentation/cfd.png)

- This chart show the number of tasks cumulatively for each column over the time.
- Everyday, the total number of tasks is recorded for each column.
- If you would like to exclude closed tasks, change the [global project settings](project-configuration.markdown).

Note: You need to have at least 2 days of data to see the graph.

Burndown chart
--------------

![Burndown chart](http://kanboard.net/screenshots/documentation/burndown-chart.png)

The [burn down chart](http://en.wikipedia.org/wiki/Burn_down_chart) is available for each project.

- This chart is a graphical representation of work left to do versus time.
- Kanboard use the complexity or story point to generate this diagram. 
- Everyday, the sum of the story points for each column is calculated.

Average time spent into each column
-----------------------------------

![Average time spent into each column](http://kanboard.net/screenshots/documentation/average-time-spent-into-each-column.png)

This chart show the average time spent into each column for the last 1000 tasks.

- Kanboard use the task transitions to calculate the data.
- The time spent is calculated until the task is closed.

Average Lead and Cycle time
---------------------------

![Average time spent into each column](http://kanboard.net/screenshots/documentation/average-lead-cycle-time.png)

This chart show the average lead and cycle time for the last 1000 tasks over the time.

- The lead time is the time between the task creation and the date of completion.
- The cycle time is time between the specified start date of the task to completion date.
- If the task is not closed, the current time is used instead of the date of completion.

Those metrics are calculated and recorded everyday for the whole project.

Don't forget to run the daily job for stats calculation
-------------------------------------------------------

To generate accurate analytics data, you should run the daily cronjob **project daily statistics**.

[Read the documentation about Kanboard CLI](cli.markdown)
