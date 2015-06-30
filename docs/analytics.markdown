Analytics
=========

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

This chart show the number of tasks cumulatively for each column over the time.

Note: You need to have at least 2 days of data to see the graph.

Burndown chart
--------------

![Burndown chart](http://kanboard.net/screenshots/documentation/burndown-chart.png)

The [burn down chart](http://en.wikipedia.org/wiki/Burn_down_chart) is available for each project.
This chart is a graphical representation of work left to do versus time.

Kanboard use the complexity or story point to generate this diagram.

Don't forget to run the daily job for stats calculation
-------------------------------------------------------

To generate accurate analytics data, you should run the daily cronjob **project daily summaries** just before midnight.

[Read the documentation about Kanboard CLI](http://kanboard.net/documentation/cli)
