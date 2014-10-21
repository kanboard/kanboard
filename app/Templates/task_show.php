<?= Helper\template('task_details', array('task' => $task, 'project' => $project)) ?>
<?= Helper\template('task_time', array('values' => $values, 'date_format' => $date_format, 'date_formats' => $date_formats)) ?>
<?= Helper\template('task_show_description', array('task' => $task)) ?>
<?= Helper\template('subtask_show', array('task' => $task, 'subtasks' => $subtasks)) ?>
<?= Helper\template('task_timesheet', array('timesheet' => $timesheet)) ?>
<?= Helper\template('file_show', array('task' => $task, 'files' => $files)) ?>
<?= Helper\template('task_comments', array('task' => $task, 'comments' => $comments, 'project' => $project)) ?>