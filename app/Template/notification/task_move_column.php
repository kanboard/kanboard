<html>
<body>
<h2><?= $this->text->e($task['title']) ?> (#<?= $task['id'] ?>)</h2>

<ul>
    <li>
        <?= t('Column on the board:') ?>
        <strong><?= $this->text->e($task['column_title']) ?></strong>
    </li>
    <li><?= t('Task position:').' '.$this->text->e($task['position']) ?></li>
</ul>

<?= $this->render('notification/footer', array('task' => $task)) ?>
</body>
</html>