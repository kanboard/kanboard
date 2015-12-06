<section id="main" class="public-task">

    <?= $this->render('task/details', array('task' => $task, 'project' => $project, 'editable' => false)) ?>

    <p class="pull-right"><?= $this->url->link(t('Back to the board'), 'board', 'readonly', array('token' => $project['token'])) ?></p>

    <?= $this->render('task/description', array(
        'task' => $task,
        'project' => $project,
        'is_public' => true,
    )) ?>

    <?= $this->render('tasklink/show', array(
        'task' => $task,
        'links' => $links,
        'project' => $project,
        'editable' => false,
        'is_public' => true,
    )) ?>

    <?= $this->render('subtask/show', array(
        'task' => $task,
        'subtasks' => $subtasks,
        'editable' => false
    )) ?>

    <?= $this->render('task/comments', array(
        'task' => $task,
        'comments' => $comments,
        'project' => $project,
        'editable' => false,
        'is_public' => true,
    )) ?>

</section>