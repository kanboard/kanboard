<section id="main" class="public-task">

    <?= $this->render('task/details', array('task' => $task, 'project' => $project)) ?>

    <p class="pull-right"><?= $this->a(t('Back to the board'), 'board', 'readonly', array('token' => $project['token'])) ?></p>

    <?= $this->render('task/show_description', array(
        'task' => $task,
        'project' => $project,
        'is_public' => true
    )) ?>

    <?= $this->render('subtask/show', array(
        'task' => $task,
        'subtasks' => $subtasks,
        'not_editable' => true
    )) ?>

    <?= $this->render('task/comments', array(
        'task' => $task,
        'comments' => $comments,
        'project' => $project,
        'not_editable' => true,
        'is_public' => true,
    )) ?>

</section>