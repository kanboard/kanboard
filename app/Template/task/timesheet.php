<?php if ($timesheet['time_estimated'] > 0 || $timesheet['time_spent'] > 0): ?>

<div class="page-header">
    <h2><?= t('Time tracking') ?></h2>
</div>

<ul class="listing">
    <li><?= t('Estimate:') ?> <strong><?= $this->e($timesheet['time_estimated']) ?></strong> <?= t('hours') ?></li>
    <li><?= t('Spent:') ?> <strong><?= $this->e($timesheet['time_spent']) ?></strong> <?= t('hours') ?></li>
    <li><?= t('Remaining:') ?> <strong><?= $this->e($timesheet['time_remaining']) ?></strong> <?= t('hours') ?></li>
</ul>

<?php endif ?>