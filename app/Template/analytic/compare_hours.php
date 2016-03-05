<div class="page-header">
    <h2><?= t('Compare Estimated Time vs Actual Time') ?></h2>
</div>

<div class="listing">
    <ul>
        <li><?= t('Estimated hours: ').'<strong>'.$this->text->e($metrics['open']['time_estimated'] + $metrics['closed']['time_estimated']) ?></strong></li>
        <li><?= t('Actual hours: ').'<strong>'.$this->text->e($metrics['open']['time_spent'] + $metrics['closed']['time_spent']) ?></strong></li>
    </ul>
</div>

<?php if (empty($metrics)): ?>
    <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
<?php else: ?>
<section id="analytic-compare-hours">
    <div id="chart"
        data-metrics='<?= json_encode($metrics, JSON_HEX_APOS)?>'
        data-label-spent="<?= t('Hours Spent') ?>"
        data-label-estimated="<?= t('Hours Estimated') ?>"
        data-label-closed="<?= t('Closed') ?>"
        data-label-open="<?= t('Open') ?>"></div>

    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('No tasks found.') ?></p>
    <?php elseif (! $paginator->isEmpty()): ?>
        <table class="table-fixed table-small">
            <tr>
                <th class="column-5"><?= $paginator->order(t('Id'), 'tasks.id') ?></th>
                <th><?= $paginator->order(t('Title'), 'tasks.title') ?></th>
                <th class="column-5"><?= $paginator->order(t('Status'), 'tasks.is_active') ?></th>
                <th class="column-10"><?= $paginator->order(t('Estimated Time'), 'tasks.time_estimated') ?></th>
                <th class="column-10"><?= $paginator->order(t('Actual Time'), 'tasks.time_spent') ?></th>
            </tr>
            <?php foreach ($paginator->getCollection() as $task): ?>
            <tr>
                <td class="task-table color-<?= $task['color_id'] ?>">
                    <?= $this->url->link('#'.$this->text->e($task['id']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', t('View this task')) ?>
                </td>
                <td>
                    <?= $this->url->link($this->text->e($task['title']), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', t('View this task')) ?>
                </td>
                <td>
                    <?php if ($task['is_active'] == \Kanboard\Model\Task::STATUS_OPEN): ?>
                        <?= t('Open') ?>
                    <?php else: ?>
                        <?= t('Closed') ?>
                    <?php endif ?>
                </td>
                <td>
                    <?= $this->text->e($task['time_estimated']) ?>
                </td>
                <td>
                    <?= $this->text->e($task['time_spent']) ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>

        <?= $paginator ?>
    <?php endif ?>
</section>
<?php endif ?>
