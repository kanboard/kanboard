<div class="page-header">
    <h2><?= t('Time Tracking') ?></h2>
</div>

<h3><?= t('Subtask timesheet') ?></h3>
<?php if ($subtask_paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is nothing to show.') ?></p>
<?php else: ?>
    <table class="table-fixed table-scrolling table-striped">
        <tr>
            <th class="column-25"><?= $subtask_paginator->order(t('Task'), 'task_title') ?></th>
            <th class="column-25"><?= $subtask_paginator->order(t('Subtask'), 'subtask_title') ?></th>
            <th class="column-20"><?= $subtask_paginator->order(t('Start'), 'start') ?></th>
            <th class="column-20"><?= $subtask_paginator->order(t('End'), 'end') ?></th>
            <th class="column-10"><?= $subtask_paginator->order(t('Time spent'), 'time_spent') ?></th>
        </tr>
        <?php foreach ($subtask_paginator->getCollection() as $record): ?>
        <tr>
            <td><?= $this->url->link($this->text->e($record['task_title']), 'TaskViewController', 'show', array('project_id' => $record['project_id'], 'task_id' => $record['task_id'])) ?></td>
            <td><?= $this->url->link($this->text->e($record['subtask_title']), 'TaskViewController', 'show', array('project_id' => $record['project_id'], 'task_id' => $record['task_id'])) ?></td>
            <td><?= $this->dt->datetime($record['start']) ?></td>
            <td><?= $this->dt->datetime($record['end']) ?></td>
            <td><?= n($record['time_spent']).' '.t('hours') ?></td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= $subtask_paginator ?>
<?php endif ?>
