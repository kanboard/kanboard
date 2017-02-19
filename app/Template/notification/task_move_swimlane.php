<h2><?= $this->text->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<ul>
    <li>
        <?php if ($task['swimlane_id'] == 0): ?>
            <?= t('The task have been moved to the first swimlane') ?>
        <?php else: ?>
            <?= t('The task have been moved to another swimlane:') ?>
            <strong><?= $this->text->e($task['swimlane_name']) ?></strong>
        <?php endif ?>
    </li>
    <li>
        <?= t('Column on the board:') ?>
        <strong><?= $this->text->e($task['column_title']) ?></strong>
    </li>
    <li><?= t('Task position:').' '.$this->text->e($task['position']) ?></li>
</ul>

<?= $this->render('notification/footer', array('task' => $task)) ?>