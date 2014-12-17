<?= Helper\template('task/details', array('task' => $task, 'project' => $project, 'links' => $links)) ?>
<?= Helper\template('task/time', array('values' => $values, 'date_format' => $date_format, 'date_formats' => $date_formats)) ?>
<?= Helper\template('task/show_description', array('task' => $task)) ?>
<?= Helper\template('tasklink/show', array('task' => $task, 'links' => $links, 'link_list' => $link_list, 'task_list' => $task_list)) ?>
<?= Helper\template('subtask/show', array('task' => $task, 'subtasks' => $subtasks)) ?>
<?= Helper\template('task/timesheet', array('timesheet' => $timesheet)) ?>
<?= Helper\template('file/show', array('task' => $task, 'files' => $files)) ?>
<?= Helper\template('task/comments', array('task' => $task, 'comments' => $comments, 'project' => $project)) ?>