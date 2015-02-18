<?= $this->render('task/timesheet', array('task' => $task)) ?>

<h3><?= t('Subtask timesheet') ?></h3>
<?php if ($subtask_paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is nothing to show.') ?></p>
<?php else: ?>
    <table class="table-fixed">
        <tr>
            <th class="column-20"><?= $subtask_paginator->order(t('User'), 'username') ?></th>
            <th class="column-30"><?= $subtask_paginator->order(t('Subtask'), 'subtask_title') ?></th>
            <th><?= $subtask_paginator->order(t('Start'), 'start') ?></th>
            <th><?= $subtask_paginator->order(t('End'), 'end') ?></th>
        </tr>
        <?php foreach ($subtask_paginator->getCollection() as $record): ?>
        <tr>
            <td><?= $this->a($this->e($record['user_fullname'] ?: $record['username']), 'user', 'show', array('user_id' => $record['user_id'])) ?></td>
            <td><?= t($record['subtask_title']) ?></td>
            <td><?= dt('%B %e, %Y at %k:%M %p', $record['start']) ?></td>
            <td><?= dt('%B %e, %Y at %k:%M %p', $record['end']) ?></td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= $subtask_paginator ?>
<?php endif ?>