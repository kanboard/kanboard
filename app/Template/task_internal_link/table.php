<?php if (! empty($links)): ?>
<table class="task-links-table table-stripped">
    <?php foreach ($links as $label => $grouped_links): ?>
        <?php $hide_td = false ?>
        <?php foreach ($grouped_links as $link): ?>
            <?php if (! $hide_td): ?>
                <tr>
                    <td class="column-40" colspan="2">
                        <?= t('This task') ?>
                        <strong><?= t($label) ?></strong>
                        <span class="task-links-task-count">(<?= count($grouped_links) ?>)</span>
                    </td>
                    <th><?= t('Assignee') ?></th>
                    <th><?= t('Time tracking') ?></th>
                    <?php if ($editable): ?>
                        <th class="column-5"></th>
                    <?php endif ?>
                </tr>
                <?php $hide_td = true ?>
            <?php endif ?>

        <tr>
            <td>
                <?php if ($is_public): ?>
                    <?= $this->url->link(
                        $this->text->e('#'.$link['task_id'].' '.$link['title']),
                        'task',
                        'readonly',
                        array('task_id' => $link['task_id'], 'token' => $project['token']),
                        false,
                        $link['is_active'] ? '' : 'task-link-closed'
                    ) ?>
                <?php else: ?>
                    <?= $this->url->link(
                        $this->text->e('#'.$link['task_id'].' '.$link['title']),
                        'task',
                        'show',
                        array('task_id' => $link['task_id'], 'project_id' => $link['project_id']),
                        false,
                        $link['is_active'] ? '' : 'task-link-closed'
                    ) ?>
                <?php endif ?>

                <?php if ($link['project_id'] != $project['id']): ?>
                    <br>
                    <?= $this->text->e($link['project_name']) ?>
                <?php endif ?>
            </td>
            <td>
                <?= $this->text->e($link['column_title']) ?>
            </td>
            <td>
                <?php if (! empty($link['task_assignee_username'])): ?>
                    <?php if ($editable): ?>
                        <?= $this->url->link($this->text->e($link['task_assignee_name'] ?: $link['task_assignee_username']), 'user', 'show', array('user_id' => $link['task_assignee_id'])) ?>
                    <?php else: ?>
                        <?= $this->text->e($link['task_assignee_name'] ?: $link['task_assignee_username']) ?>
                    <?php endif ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (! empty($link['task_time_spent'])): ?>
                    <strong><?= $this->text->e($link['task_time_spent']).'h' ?></strong> <?= t('spent') ?>
                <?php endif ?>

                <?php if (! empty($link['task_time_estimated'])): ?>
                    <strong><?= $this->text->e($link['task_time_estimated']).'h' ?></strong> <?= t('estimated') ?>
                <?php endif ?>
            </td>
            <?php if ($editable && $this->user->hasProjectAccess('Tasklink', 'edit', $task['project_id'])): ?>
            <td>
                <div class="dropdown">
                <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
                <ul>
                    <li><?= $this->url->link(t('Edit'), 'TaskInternalLink', 'edit', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?></li>
                    <li><?= $this->url->link(t('Remove'), 'TaskInternalLink', 'confirm', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?></li>
                </ul>
                </div>
            </td>
            <?php endif ?>
        </tr>
        <?php endforeach ?>
    <?php endforeach ?>
</table>
<?php endif ?>