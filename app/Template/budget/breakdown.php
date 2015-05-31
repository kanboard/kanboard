<div class="page-header">
    <h2><?= t('Cost breakdown') ?></h2>
</div>

<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('There is nothing to show.') ?></p>
<?php else: ?>
    <table class="table-fixed">
        <tr>
            <th class="column-20"><?= $paginator->order(t('Task'), 'task_title') ?></th>
            <th class="column-25"><?= $paginator->order(t('Subtask'), 'subtask_title') ?></th>
            <th class="column-20"><?= $paginator->order(t('User'), 'username') ?></th>
            <th class="column-10"><?= t('Cost') ?></th>
            <th class="column-10"><?= $paginator->order(t('Time spent'), \Model\SubtaskTimeTracking::TABLE.'.time_spent') ?></th>
            <th class="column-15"><?= $paginator->order(t('Date'), 'start') ?></th>
        </tr>
        <?php foreach ($paginator->getCollection() as $record): ?>
        <tr>
            <td><?= $this->url->link($this->e($record['task_title']), 'task', 'show', array('project_id' => $project['id'], 'task_id' => $record['task_id'])) ?></td>
            <td><?= $this->url->link($this->e($record['subtask_title']), 'task', 'show', array('project_id' => $project['id'], 'task_id' => $record['task_id'])) ?></td>
            <td><?= $this->url->link($this->e($record['name'] ?: $record['username']), 'user', 'show', array('user_id' => $record['user_id'])) ?></td>
            <td><?= n($record['cost']) ?></td>
            <td><?= n($record['time_spent']).' '.t('hours') ?></td>
            <td><?= dt('%B %e, %Y', $record['start']) ?></td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= $paginator ?>
<?php endif ?>