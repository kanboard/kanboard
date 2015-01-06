<h2><?= $this->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<ul>
    <li>
        <?= t('Column on the board:') ?>
        <strong><?= $this->e($task['column_title']) ?></strong>
    </li>
    <li><?= t('Task position:').' '.$this->e($task['position']) ?></li>
</ul>

<?= $this->render('notification/footer', array('task' => $task, 'application_url' => $application_url)) ?>