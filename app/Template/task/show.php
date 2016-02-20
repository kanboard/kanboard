<?= $this->render('task/details', array(
    'task' => $task,
    'project' => $project,
    'editable' => $this->user->hasProjectAccess('taskmodification', 'edit', $project['id']),
)) ?>

<?= $this->render('task/description', array('task' => $task)) ?>

<?= $this->render('subtask/show', array(
    'task' => $task,
    'subtasks' => $subtasks,
    'project' => $project,
    'users_list' => isset($users_list) ? $users_list : array(),
    'editable' => true,
)) ?>

<?= $this->render('tasklink/show', array(
    'task' => $task,
    'links' => $links,
    'link_label_list' => $link_label_list,
    'editable' => true,
    'is_public' => false,
)) ?>

<?= $this->render('task/time_tracking_summary', array('task' => $task)) ?>

<?= $this->render('task_file/show', array(
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
