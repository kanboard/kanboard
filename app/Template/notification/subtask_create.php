<h2><?= $this->text->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<h3><?= t('New sub-task') ?></h3>

<ul>
    <li><?= t('Title:') ?> <?= $this->text->e($subtask['title']) ?></li>
    <li><?= t('Status:') ?> <?= $this->text->e($subtask['status_name']) ?></li>
    <li><?= t('Assignee:') ?> <?= $this->text->e($subtask['name'] ?: $subtask['username'] ?: '?') ?></li>
    <li>
        <?= t('Time tracking:') ?>
        <?php if (! empty($subtask['time_estimated'])): ?>
            <strong><?= $this->text->e($subtask['time_estimated']).'h' ?></strong> <?= t('estimated') ?>
        <?php endif ?>
    </li>
</ul>

<?= $this->render('notification/footer', array('task' => $task)) ?>