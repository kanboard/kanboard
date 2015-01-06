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
                    <?= Helper\escape($link['name']) ?>
                <?php endif ?>
            </td>
            <td>
                <?php if (0 == $link['task_inverse_is_active']): ?><span class="task-closed"><?php endif ?>
                <?= Helper\escape($link['task_inverse_category']) ?>
                <?php if (! isset($not_editable)): ?>
                    <?= Helper\a('#'.Helper\escape($link['task_inverse_id']).' - '.trim(Helper\escape($link['task_inverse_name'])), 'task', 'show', array('task_id' => $link['task_inverse_id'])) ?>
                <?php else: ?>
                    <?= Helper\a('#'.Helper\escape($link['task_inverse_id']).' - '.trim(Helper\escape($link['task_inverse_name'])), 'task', 'readonly', array('task_id' => $link['task_inverse_id'], 'token' => $project['token'])) ?>
                <?php endif ?>
                <?php if (0 == $link['task_inverse_is_active']): ?></span><?php endif ?>
            </td>
            <?php if (! isset($not_editable)): ?>
            <td>
                <?= Helper\a(t('Edit'), 'tasklink', 'edit', array('task_id' => $task['id'], 'link_id' => $link['id'])) ?>
                <?= t('or') ?>
                <?= Helper\a(t('Remove'), 'tasklink', 'confirm', array('task_id' => $task['id'], 'link_id' => $link['id'])) ?>
            </td>
            <?php endif ?>
        </tr>
        <?php endforeach ?>
    </table>

    <?php if (! isset($not_editable) && !empty($link_list)): ?>
    <form method="post" action="<?= Helper\u('tasklink', 'save', array('task_id' => $task['id'])) ?>" autocomplete="off">
        <?= Helper\form_csrf() ?>
        <?= Helper\form_hidden('task_id', array('task_id' => $task['id'])) ?>
        #<?= Helper\escape($task['id']) ?>
        <?= Helper\form_select('link_id', $link_list, array(), array(), 'required autofocus') ?>
        #<?= Helper\form_numeric('task_inverse_id', array(), array(), array('required', 'placeholder="'.t('Task id').'"', 'list="task_inverse_ids"')) ?>
        <datalist id="task_inverse_ids">
            <select>
                <?php foreach ($task_list as $task_inverse_id => $task_inverse_title): ?>
                <option value="<?= $task_inverse_id ?>">#<?= $task_inverse_id.' '.$task_inverse_title ?></option>
                <?php endforeach ?>
            </select>
        </datalist>
        <input type="submit" value="<?= t('Add') ?>" class="btn btn-blue"/>
    </form>
    <?php endif ?>
</aside>
<?php endif ?>
