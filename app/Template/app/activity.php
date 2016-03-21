<div class="page-header">
    <h2><?= t('My activity stream') ?></h2>
</div>
<?php if (!$events->isEmpty()): ?>
<div class="page-header">
    <ul>
        <li>
            <?= $events->order(t('Order by Date'), 'id') ?>
        </li>
        <li>
            <?= $events->order(t('Order by Task'), 'task_id') ?>
        </li>
    </ul>
</div>
<?php endif ?>
<?= $this->render('event/events', array('events' => $events)) ?>