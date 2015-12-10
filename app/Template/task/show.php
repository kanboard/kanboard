<?= $this->render('task/details', array(
    'task' => $task,
    'project' => $project,
    'recurrence_trigger_list' => $this->task->recurrenceTriggers(),
    'recurrence_timeframe_list' => $this->task->recurrenceTimeframes(),
    'recurrence_basedate_list' => $this->task->recurrenceBasedates(),
    'editable' => $this->user->hasProjectAccess('taskmodification', 'edit', $project['id']),
)) ?>

<?php if ($this->user->hasProjectAccess('taskmodification', 'edit', $project['id'])): ?>
    <?= $this->render('task_modification/edit_time', array('task' => $task, 'values' => $values, 'date_format' => $date_format, 'date_formats' => $date_formats)) ?>
<?php endif ?>

<?= $this->render('task/description', array('task' => $task)) ?>

<?= $this->render('tasklink/show', array(
    'task' => $task,
    'links' => $links,
    'link_label_list' => $link_label_list,
    'editable' => $this->user->hasProjectAccess('tasklink', 'edit', $project['id']),
    'is_public' => false,
)) ?>

<?= $this->render('subtask/show', array(
    'task' => $task,
    'subtasks' => $subtasks,
    'project' => $project,
    'users_list' => isset($users_list) ? $users_list : array(),
    'editable' => $this->user->hasProjectAccess('subtask', 'edit', $project['id']),
)) ?>

<?= $this->render('task/time_tracking_summary', array('task' => $task)) ?>

<?= $this->render('file/show', array(
    'task' => $task,
    'files' => $files,
    'images' => $images
)) ?>

<?= $this->render('task/comments', array(
    'task' => $task,
    'comments' => $comments,
    'project' => $project,
    'editable' => $this->user->hasProjectAccess('comment', 'edit', $project['id']),
)) ?>
