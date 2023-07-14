<?php if (! empty($links)): ?>
<table class="task-links-table table-striped table-scrolling">
    <?php foreach ($links as $label => $grouped_links): ?>
        <?php $hide_td = false ?>
        <?php foreach ($grouped_links as $link): ?>
            <?php if (! $hide_td): ?>
                <tr>
                    <th>
                        <?= t('This task') ?>
                        <em><?= t($label) ?></em>
                        <span class="task-links-task-count">(<?= count($grouped_links) ?>)</span>
                    </th>
                    <th class="column-10"><?= t('Assignee') ?></th>
                    <th class="column-30"><?= t('Time tracking') ?></th>
                </tr>
                <?php $hide_td = true ?>
            <?php endif ?>
        <tr>
            <td>
                <?php if ($is_public): ?>
                    <?= $this->url->link(
                        $this->text->e('#'.$link['task_id'].' '.$link['title']),
                        'TaskViewController',
                        'readonly',
                        array('task_id' => $link['task_id'], 'token' => $project['token']),
                        false,
                        $link['is_active'] ? '' : 'task-link-closed'
                    ) ?>
                <?php else: ?>
                    <?php if ($editable && $this->user->hasProjectAccess('Tasklink', 'edit', $task['project_id'])): ?>
                        <div class="dropdown">
                            <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></a>
                            <ul>
                                <li>
                                    <?= $this->modal->medium('edit', t('Edit'), 'TaskInternalLinkController', 'edit', array('link_id' => $link['id'], 'task_id' => $task['id'])) ?>
                                </li>
                                <li>
                                    <?= $this->modal->confirm('trash-o', t('Remove'), 'TaskInternalLinkController', 'confirm', array('link_id' => $link['id'], 'task_id' => $task['id'])) ?>
                                </li>
                            </ul>
                        </div>
                    <?php endif ?>

                    <?= $this->url->link(
                        $this->text->e('#'.$link['task_id'].' '.$link['title']),
                        'TaskViewController',
                        'show',
                        array('task_id' => $link['task_id']),
                        false,
                        $link['is_active'] ? '' : 'task-link-closed'
                    ) ?>
                <?php endif ?>

                (<?php if ($link['project_id'] != $project['id']): ?><?= $this->text->e($link['project_name']) ?> - <?php endif ?><?= $this->text->e($link['column_title']) ?>)
            </td>
            <td>
                <?php if (! empty($link['task_assignee_username'])): ?>
                    <?php if ($editable): ?>
                        <?= $this->url->link($this->text->e($link['task_assignee_name'] ?: $link['task_assignee_username']), 'UserViewController', 'show', array('user_id' => $link['task_assignee_id'])) ?>
                    <?php else: ?>
                        <?= $this->text->e($link['task_assignee_name'] ?: $link['task_assignee_username']) ?>
                    <?php endif ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (! empty($link['task_time_spent'])): ?>
                    <?= t('%sh spent', n($link['task_time_spent'])) ?>
                <?php endif ?>
                <?php if (! empty($link['task_time_spent']) && ! empty($link['task_time_estimated'])): ?>/<?php endif ?>
                <?php if (! empty($link['task_time_estimated'])): ?>
                    <?= t('%sh estimated', n($link['task_time_estimated'])) ?>
                <?php endif ?>
            </td>
        </tr>
        <?php endforeach ?>
    <?php endforeach ?>
</table>
<?php endif ?>
