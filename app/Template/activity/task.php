<?= $this->render('task/details', array(
    'task' => $task,
    'tags' => $tags,
    'project' => $project,
    'editable' => false,
    'assignees' => $assignees,
)) ?>

<div class="page-header">
    <h2><?= t('Activity stream') ?></h2>
</div>

<?= $this->render('event/events', array('events' => $events)) ?>
