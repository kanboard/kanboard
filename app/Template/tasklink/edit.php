<div class="page-header">
    <?php if (! isset($edit)): ?>
    <h2><?= t('Add a link') ?></h2>
    <?php else: ?>
    <h2><?= t('Edit a link') ?></h2>
    <?php endif ?>
</div>

<?php if (!empty($link_list)): ?>
<form method="post" action="<?= $this->u('tasklink', isset($edit) ? 'update' : 'save', array('task_id' => $task['id'], 'link_id' => @$values['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?php if (isset($edit)): ?>
    <?= $this->formHidden('id', $values) ?>
    <?= $this->formHidden('task_link_inverse_id', $values) ?>
    <?php endif ?>
    <?= $this->formHidden('task_id', $values) ?>

    #<?= $task['id'] ?>
    &#160;
    <?= $this->formSelect('link_label_id', $link_list, $values, $errors, 'required autofocus') ?>
    &#160;
    #<?= $this->formNumeric('task_inverse_id', $values, $errors, array('required', 'placeholder="'.t('Task id').'"', 'title="'.t('Linked task id').'"', 'list="task_inverse_ids"')) ?>
    <?php if (!empty($task_list)): ?>
    <datalist id="task_inverse_ids">
    <select>
        <?php foreach ($task_list as $task_inverse_id => $task_inverse_title): ?>
        <option value="<?= $task_inverse_id ?>">#<?= $task_inverse_id.' '.$task_inverse_title ?></option>
        <?php endforeach ?>
    </select>
    </datalist>
    <?php endif ?>
    <br/>

    <?php if (! isset($edit)): ?>
    <?= $this->formCheckbox('another_link', t('Create another link'), 1, isset($values['another_link']) && $values['another_link'] == 1) ?>
    <?php endif ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</form>
<?php else: ?>
<div class="alert alert-info">
    <?= t('You need to add link labels to this project before to link this task to another one.') ?>
    <ul>
        <li><?= $this->a(t('Add link labels'), 'link', 'index', array('project_id' => $task['project_id'])) ?></li>
    </ul>
</div>
<?php endif ?>
