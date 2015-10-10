<?= $this->render('task/time_tracking_summary', array('task' => $task)) ?>

<h3><?= t('Subtask timesheet') ?></h3>
<?php if ($subtask_paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is nothing to show.') ?></p>
<?php else: ?>
    <table class="table-fixed">
        <tr>
            <th class="column-15"><?= $subtask_paginator->order(t('User'), 'username') ?></th>
            <th><?= $subtask_paginator->order(t('Subtask'), 'subtask_title') ?></th>
            <th class="column-20"><?= $subtask_paginator->order(t('Start'), 'start') ?></th>
            <th class="column-20"><?= $subtask_paginator->order(t('End'), 'end') ?></th>
            <th class="column-10"><?= $subtask_paginator->order(t('Time spent'), 'time_spent') ?></th>
        </tr>
        <?php foreach ($subtask_paginator->getCollection() as $record): ?>
        <tr>
            <td><?= $this->url->link($this->e($record['user_fullname'] ?: $record['username']), 'user', 'show', array('user_id' => $record['user_id'])) ?></td>
            <td><?= t($record['subtask_title']) ?></td>
            <td><?= dt('%B %e, %Y at %k:%M %p', $record['start']) ?></td>
            <td><?= dt('%B %e, %Y at %k:%M %p', $record['end']) ?></td>
            <td><?= n($record['time_spent']).' '.t('hours') ?></td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= $subtask_paginator ?>
<?php endif ?>