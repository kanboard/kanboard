<?php if (! empty($links)): ?>
<div class="page-header">
    <h2><?= t('Links') ?></h2>
</div>
<table class="table-fixed" id="links">
    <tr>
        <th class="column-30"><?= t('Label') ?></th>
        <th class="column-40"><?= t('Task') ?></th>
        <th class="column-20"><?= t('Column') ?></th>
        <?php if (! isset($not_editable)): ?>
            <th><?= t('Action') ?></th>
        <?php endif ?>
    </tr>
    <?php foreach ($links as $link): ?>
    <tr>
        <td><?= t('This task') ?> <strong><?= t($link['label']) ?></strong></td>
        <?php if (! isset($not_editable)): ?>
            <td>
                <?= $this->a(
                    $this->e('#'.$link['task_id'].' - '.$link['title']),
                    'task', 'show', array('task_id' => $link['task_id'], 'project_id' => $link['project_id']),
                    false,
                    $link['is_active'] ? '' : 'task-link-closed'
                ) ?>
            </td>
            <td><?= $this->e($link['column_title']) ?></td>
            <td>
                <?= $this->a(t('Remove'), 'tasklink', 'confirm', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            </td>
        <?php else: ?>
            <td>
                <?= $this->a(
                    $this->e('#'.$link['task_id'].' - '.$link['title']),
                    'task', 'readonly', array('task_id' => $link['task_id'], 'token' => $project['token']),
                    false,
                    $link['is_active'] ? '' : 'task-link-closed'
                ) ?>
            </td>
            <td><?= $this->e($link['column_title']) ?></td>
        <?php endif ?>
    </tr>
    <?php endforeach ?>
</table>
<?php endif ?>