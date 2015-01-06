<div class="page-header">
    <h2><?= t('Add a link') ?></h2>
</div>

<?php if (!empty($link_list)): ?>
<form method="post" action="<?= $this->u('tasklink', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formHidden('task_id', $values) ?>

    #<?= $task['id'] ?>
    &#160;
    <?= $this->formSelect('link_id', $link_list, $values, $errors, 'required autofocus') ?>
    &#160;
    #<?= $this->formNumeric('task_inverse_id', $values, $errors, array('required', 'placeholder="'.t('Task id').'"', 'title="'.t('Linked task id').'"', 'list="task_inverse_ids"')) ?>
 	<datalist id="task_inverse_ids">
	  <select>
        <?php foreach ($task_list as $task_inverse_id => $task_inverse_title): ?>
        <option value="<?= $task_inverse_id ?>">#<?= $task_inverse_id.' '.$task_inverse_title ?></option>
        <?php endforeach ?>
	  </select>
	</datalist>
	<br/>
		
    <?= $this->formCheckbox('another_link', t('Create another link'), 1, isset($values['another_link']) && $values['another_link'] == 1) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</form>
<?php else: ?>
<div class="alert alert-info">
    You need to <?= $this->a('add link labels', 'link', 'index', array('project_id' => $task['project_id'])) ?> to this project before to link this task to another one.
</div>
<?php endif ?>