<?php if (! empty($links)): ?>
<div id="links" class="task-show-section">

    <div class="page-header">
        <h2><?= t('Links') ?></h2>
    </div>

    <table class="links-table">
        <tr>
            <th class="column-40"><?= t('Label') ?> / <?= t('Task') ?></th>
            <th class="column-10"><?= t('Column') ?></th>
            <th><?= t('Assignee') ?></th>
            <th><?= t('Time tracking') ?></th>
            <?php if (! isset($not_editable)): ?>
                <th class="column-10"><?= t('Actions') ?></th>
            <?php endif ?>
        </tr>
        <?php foreach ($links as $label => $grouped_links): ?>
            <tr>
                <td colspan="<?= (isset($not_editable) ? '4' : '5') ?>" class="links-label"><?= t('This task') ?> <strong><?= t($label) ?></strong></td>
            </tr>
            <?php foreach ($grouped_links as $link): ?>
            <tr>
                <td>
                    <?php if (! isset($not_editable)): ?>
                        <?= $this->a(
                            $this->e('#'.$link['task_id'].' '.$link['title']),
                            'task',
                            'show',
                            array('task_id' => $link['task_id'], 'project_id' => $link['project_id']),
                            false,
                            $link['is_active'] ? '' : 'task-link-closed'
                        ) ?>
                    <?php else: ?>
                        <?= $this->a(
                            $this->e('#'.$link['task_id'].' '.$link['title']),
                            'task',
                            'readonly',
                            array('task_id' => $link['task_id'], 'token' => $project['token']),
                            false,
                            $link['is_active'] ? '' : 'task-link-closed'
                        ) ?>
                    <?php endif ?>
                </td>
                <td><?= $this->e($link['column_title']) ?></td>
                <td>
                    <?php if (! empty($link['task_assignee_name'])): ?>
                        <?= $this->a($this->e($link['task_assignee_name'] ?: $link['task_assignee_username']), 'user', 'show', array('user_id' => $link['task_assignee_id'])) ?>
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
                        <li><?= $this->a(t('Edit'), 'tasklink', 'edit', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id'])) ?></li>
                        <li><?= $this->a(t('Remove'), 'tasklink', 'confirm', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id'])) ?></li>
                    </ul>
                </td>
                <?php endif ?>
            </tr>
            <?php endforeach ?>
        <?php endforeach ?>
    </table>

    <?php if (! isset($not_editable) && isset($link_label_list)): ?>
        <form action="<?= $this->u('tasklink', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" method="post" autocomplete="off">

            <?= $this->formCsrf() ?>
            <?= $this->formHidden('task_id', array('task_id' => $task['id'])) ?>

            <?= $this->formSelect('link_id', $link_label_list, array(), array()) ?>

            <span class="opposite_task_id_bloc">
                #<?= $this->formNumeric('opposite_task_id', array(), array(), array('required', 'placeholder="'.t('Task id').'"'), 'opposite_task_id') ?>
            </span>
            <?= $this->formText(
                'title',
                array(),
                array(),
                array(
                    'required',
                    'style="display:none"',
                    'placeholder="'.t('Start to type task title...').'"',
                    'title="'.t('Start to type task title...').'"',
                    'data-dst-field="opposite_task_id"',
                    'data-search-url="'.$this->u('app', 'autocomplete', array('exclude_task_id' => $task['id'])).'"'
                ),
                'task-autocomplete') ?>

            <input type="submit" value="<?= t('Add') ?>" class="btn btn-blue"/>
        </form>
    <?php endif ?>

</div>
<?php endif ?>
