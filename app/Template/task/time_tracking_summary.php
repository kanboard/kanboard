<?php if ($task['time_estimated'] > 0 || $task['time_spent'] > 0): ?>
    <div class="page-header">
        <h2><?= t('Time tracking') ?></h2>
    </div>

    <div class="panel">
        <ul>
            <li><?= t('Estimate:') ?> <strong><?= $this->text->e($task['time_estimated']) ?></strong> <?= t('hours') ?></li>
            <li><?= t('Spent:') ?> <strong><?= $this->text->e($task['time_spent']) ?></strong> <?= t('hours') ?></li>
            <li><?= t('Remaining:') ?> <strong><?= $this->text->e($task['time_estimated'] - $task['time_spent']) ?></strong> <?= t('hours') ?></li>
        </ul>
    </div>
<?php endif ?>