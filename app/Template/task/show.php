<?= Helper\template('task/details', array('task' => $task, 'project' => $project)) ?>
<?= Helper\template('task/time', array('values' => $values, 'date_format' => $date_format, 'date_formats' => $date_formats)) ?>
<?= Helper\template('task/show_description', array('task' => $task)) ?>
<?= Helper\template('subtask/show', array('task' => $task, 'subtasks' => $subtasks)) ?>
<?= Helper\template('task/timesheet', array('timesheet' => $timesheet)) ?>
<?= Helper\template('file/show', array('task' => $task, 'files' => $files)) ?>
<?= Helper\template('task/comments', array('task' => $task, 'comments' => $comments, 'project' => $project)) ?>