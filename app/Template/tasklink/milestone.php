<?php if (! empty($links)): ?>
<div id="milestone" class="task-show-section">
<div class="page-header">
    <h2><?= t('Milestone') ?></h2>
</div>
<table class="subtasks-table">
    <tr>
        <th class="column-40" colspan="2"><?= t('Title') ?></th>
        <th><?= t('Assignee') ?></th>
        <th><?= t('Time tracking') ?></th>
        <?php if (! isset($not_editable)): ?>
            <th><?= t('Action') ?></th>
        <?php endif ?>
    </tr>
    <?php foreach ($links['links'] as $link): ?>
    <tr>
        <td>
            <div class="task-board color-<?= $link['color_id'] ?>">
                <div class="task-board-collapsed<?= ($link['is_active'] ? '' : ' task-link-closed') ?>">
                <?php if (! isset($not_editable)): ?>
                    <?= $this->url->link(
                        $this->e('#'.$link['task_id'].' '.$link['title']),
                        'task',
                        'show',
                        array('task_id' => $link['task_id'], 'project_id' => $link['project_id']),
                        false,
                        'task-board-collapsed-title'
                    ) ?>
                <?php else: ?>
                <?= $this->url->link(
                    $this->e('#'.$link['task_id'].' '.$link['title']),
                    'task',
                    'readonly',
                    array('task_id' => $link['task_id'], 'token' => $project['token']),
                    false,
                    'task-board-collapsed-title'
                ) ?>
                <?php endif ?>
                </div>
            </div>
        </td>
        <td><?= $this->e($link['column_title']) ?></td>
        <td>
            <?php if (! empty($link['task_assignee_username'])): ?>
                <?= $this->url->link($this->e($link['task_assignee_name'] ?: $link['task_assignee_username']), 'user', 'show', array('user_id' => $link['task_assignee_id'])) ?>
            <?php endif ?>
        </td>
        <td>
            <?php if (! empty($link['task_time_spent'])): ?>
                <strong><?= $this->e($link['task_time_spent']).'h' ?></strong> <?= t('spent') ?>
            <?php endif ?>

            <?php if (! empty($link['task_time_estimated'])): ?>
                <strong><?= $this->e($link['task_time_estimated']).'h' ?></strong> <?= t('estimated') ?>
            <?php endif ?>
        </td>
        <?php if (! isset($not_editable)): ?>
        <td>
            <ul>
                <li><?= $this->url->link(t('Edit'), 'tasklink', 'edit', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id'])) ?></li>
                <li><?= $this->url->link(t('Remove'), 'tasklink', 'confirm', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id'])) ?></li>
            </ul>
        </td>
        <?php endif ?>
    </tr>
    <?php endforeach ?>
    <tfoot>
    <?php if (! empty($links['time_spent']) || ! empty($links['time_estimated'])): ?>
    <tr>
        <th colspan="3" class="total"><?= t('Total time tracking') ?></th>
        <td<?php if (! isset($not_editable)): ?> colspan="2"<?php endif ?>>
            <?php if (! empty($links['time_spent'])): ?>
                <strong><?= $this->e($links['time_spent']).'h' ?></strong> <?= t('spent') ?>
            <?php endif ?>

            <?php if (! empty($links['time_estimated'])): ?>
                <strong><?= $this->e($links['time_estimated']).'h' ?></strong> <?= t('estimated') ?>
            <?php endif ?>

            <?php if (! empty($links['time_spent']) && ! empty($links['time_estimated'])): ?>
                <strong><?= $this->e($links['time_estimated']-$links['time_spent']).'h' ?></strong> <?= t('remaining') ?>
            <?php endif ?>
            
            <div class="progress-bar">
                <div class="progress color-<?= $task['color_id'] ?>" style="width: <?= round($links['percentage']*100.0) ?>%;">
                    <?= round($links['percentage']*100.0) ?>%
                </div>
            </div>
        </td>
    </tr>
    <?php endif ?>
    </tfoot>
</table>

<?php if (! isset($not_editable) && isset($link_label_list)): ?>
    <form action="<?= $this->url->href('tasklink', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" method="post" autocomplete="off">

        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('task_id', array('task_id' => $task['id'])) ?>
        <?= $this->form->hidden('link_id', array('link_id' => 9)) ?>
        <?= $this->form->hidden('opposite_task_id', array()) ?>

        <?= $this->form->text(
            'title',
            array(),
            array(),
            array(
                'required',
                'placeholder="'.t('Start to type task title...').'"',
                'title="'.t('Start to type task title...').'"',
                'data-dst-field="opposite_task_id"',
                'data-search-url="'.$this->url->href('app', 'autocomplete', array('exclude_task_id' => $task['id'])).'"',
            ),
            'task-autocomplete') ?>

        <input type="submit" value="<?= t('Add') ?>" class="btn btn-blue"/>
    </form>
<?php endif ?>
</div>
<?php endif ?>
