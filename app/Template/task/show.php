<?= $this->render('task/details', array('task' => $task, 'project' => $project)) ?>
<?= $this->render('task/time', array('task' => $task, 'values' => $values, 'date_format' => $date_format, 'date_formats' => $date_formats)) ?>
<?= $this->render('task/show_description', array('task' => $task)) ?>
<?= $this->render('subtask/show', array('task' => $task, 'subtasks' => $subtasks)) ?>
<?= $this->render('task/timesheet', array('timesheet' => $timesheet)) ?>
<?= $this->render('file/show', array('task' => $task, 'files' => $files)) ?>
<?= $this->render('task/comments', array('task' => $task, 'comments' => $comments, 'project' => $project)) ?>