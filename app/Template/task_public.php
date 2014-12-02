<section id="main" class="public-task">

    <?= Helper\template('task_details', array('task' => $task, 'project' => $project)) ?>

    <p class="pull-right"><?= Helper\a(t('Back to the board'), 'board', 'readonly', array('token' => $project['token'])) ?></p>

    <?= Helper\template('task_show_description', array(
        'task' => $task,
        'project' => $project,
        'is_public' => true
    )) ?>

    <?= Helper\template('subtask/show', array(
        'task' => $task,
        'subtasks' => $subtasks,
        'not_editable' => true
    )) ?>

    <?= Helper\template('task_comments', array(
        'task' => $task,
        'comments' => $comments,
        'project' => $project,
        'not_editable' => true,
        'is_public' => true,
    )) ?>

</section>