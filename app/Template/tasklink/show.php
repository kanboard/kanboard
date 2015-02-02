<?php if (! empty($links)): ?>
<aside id="links" class="task-show-section">
    <div class="page-header">
        <h2><?= t('Links') ?></h2>
    </div>
    
    <table class="link-table">
        <tr>
            <th><?= t('Label') ?></th>
            <th width="70%"><?= t('Task') ?></th>
            <?php if (! isset($not_editable)): ?>
                <th><?= t('Actions') ?></th>
            <?php endif ?>
        </tr>
        <?php $previous_link = null;
        foreach ($links as $link): ?>
        <tr>
            <td>
                <?php if (null == $previous_link || $previous_link != $link['name']):
                    $previous_link = $link['name']; ?>
                    <?= $this->e($link['name']) ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (0 == $link['task_inverse_is_active']): ?><span class="task-closed"><?php endif ?>
                <?= $this->e($link['task_inverse_category']) ?>
                <?php if (! isset($not_editable)): ?>
                    <?= $this->a('#'.$this->e($link['task_inverse_id']).' - '.trim($this->e($link['task_inverse_name'])), 'task', 'show', array('task_id' => $link['task_inverse_id'], 'project_id' => $link['task_inverse_project_id'])) ?>
                <?php else: ?>
                    <?= $this->a('#'.$this->e($link['task_inverse_id']).' - '.trim($this->e($link['task_inverse_name'])), 'task', 'readonly', array('task_id' => $link['task_inverse_id'], 'project_id' => $link['task_inverse_project_id'], 'token' => $project['token'])) ?>
                <?php endif ?>
                <?php if (0 == $link['task_inverse_is_active']): ?></span><?php endif ?>
            </td>
            <?php if (! isset($not_editable)): ?>
            <td>
                <ul>
                    <li><?= $this->a(t('Edit'), 'tasklink', 'edit', array('task_id' => $task['id'], 'link_id' => $link['id'], 'project_id' => $task['project_id'])) ?></li>
                    <li><?= $this->a(t('Remove'), 'tasklink', 'confirm', array('task_id' => $task['id'], 'link_id' => $link['id'], 'project_id' => $task['project_id'])) ?></li>
                </ul>
            </td>
            <?php endif ?>
        </tr>
        <?php endforeach ?>
    </table>

    <?php if (! isset($not_editable) && !empty($link_list)): ?>
    <form method="post" action="<?= $this->u('tasklink', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">
        <?= $this->formCsrf() ?>
        <?= $this->formHidden('task_id', array('task_id' => $task['id'])) ?>
        #<?= $this->e($task['id']) ?>
        &#160;
        <?= $this->formSelect('link_id', $link_list, array(), array(), 'required autofocus') ?>
        &#160;
        #<?= $this->formNumeric('task_inverse_id', array(), array(), array('required', 'placeholder="'.t('Task id').'"', 'title="'.t('Linked task id').'"', 'list="task_inverse_ids"')) ?>
        <?php if (!empty($task_list)): ?>
        <datalist id="task_inverse_ids">
            <select>
                <?php foreach ($task_list as $task_inverse_id => $task_inverse_title): ?>
                <option value="<?= $task_inverse_id ?>">#<?= $task_inverse_id.' '.$task_inverse_title ?></option>
                <?php endforeach ?>
            </select>
        </datalist>
        <?php endif ?>
        <input type="submit" value="<?= t('Add') ?>" class="btn btn-blue"/>
    </form>
    <?php endif ?>
</aside>
<?php endif ?>
