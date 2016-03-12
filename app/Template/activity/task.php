<div class="task-show-title color-<?= $task['color_id'] ?>">
    <h2><?= $this->text->e($task['title']) ?></h2>
</div>

<div class="page-header">
    <h2><?= t('Activity stream') ?></h2>
</div>

<?= $this->render('event/events', array('events' => $events)) ?>