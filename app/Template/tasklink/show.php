<?php if (! empty($links)): ?>
<div class="page-header">
    <h2><?= t('Links') ?></h2>
</div>
<table id="links">
    <tr>
        <th class="column-20"><?= t('Label') ?></th>
        <th class="column-40"><?= t('Task') ?></th>
        <th><?= t('Column') ?></th>
        <?php if (! isset($not_editable)): ?>
            <th class="column-10"><?= t('Action') ?></th>
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
                <ul>
                    <li><?= $this->a(t('Edit'), 'tasklink', 'edit', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id'])) ?></li>
                    <li><?= $this->a(t('Remove'), 'tasklink', 'confirm', array('link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id'])) ?></li>
                </ul>
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
<?php endif ?>
