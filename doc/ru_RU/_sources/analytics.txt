Analytics
=========

Each project have an analytics section. Depending on how you are using
Kanboard, you can see those reports:

User repartition
----------------

.. figure:: https://kanboard.net/screenshots/documentation/user-repartition.png
   :alt: User repartition

   User repartition
This pie chart show the number of open tasks assigned per user.

Task distribution
-----------------

.. figure:: https://kanboard.net/screenshots/documentation/task-distribution.png
   :alt: Task distribution

   Task distribution
This pie chart gives an overview of the number of open tasks per column.

Cumulative flow diagram
-----------------------

.. figure:: https://kanboard.net/screenshots/documentation/cfd.png
   :alt: Cumulative flow diagram

   Cumulative flow diagram

-  This chart shows the number of tasks cumulatively for each column
   over the time.
-  Every day, the total number of tasks is recorded for each column.
-  If you would like to exclude closed tasks, change the `global project
   settings <project-configuration.markdown>`__.

Note: You need to have at least two days of data to see the graph.

Burn down chart
---------------

.. figure:: https://kanboard.net/screenshots/documentation/burndown-chart.png
   :alt: Burndown chart

   Burndown chart
The `burn down chart <http://en.wikipedia.org/wiki/Burn_down_chart>`__
is available for each project.

-  This chart is a graphical representation of work left to do versus
   time.
-  Kanboard use the complexity or story point to generate this diagram.
-  Everyday, the sum of the story points for each column is calculated.

Average time spent into each column
-----------------------------------

.. figure:: https://kanboard.net/screenshots/documentation/average-time-spent-into-each-column.png
   :alt: Average time spent into each column

   Average time spent into each column
This chart shows the average time spent into each column for the last
1000 tasks.

-  Kanboard uses the task transitions to calculate the data.
-  The time spent is calculated until the task is closed.

Average Lead and Cycle time
---------------------------

.. figure:: https://kanboard.net/screenshots/documentation/average-lead-cycle-time.png
   :alt: Average time spent into each column

   Average time spent into each column
This chart show the average lead and cycle time for the last 1000 tasks
over time.

-  The lead time is the time between the task creation and the date of
   completion.
-  The cycle time is time between the specified start date of the task
   to the completion date.
-  If the task is not closed, the current time is used instead of the
   date of completion.

Those metrics are calculated and recorded every day for the whole
project.

Note: Don't forget to run the `daily cronjob <cronjob.markdown>`__ to
have accurate statistics.
