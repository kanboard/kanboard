<div class="page-header">
    <h2><?= t('Edit a link') ?></h2>
</div>

<form method="post" action="<?= Helper\u('tasklink', 'update', array('task_id' => $task['id'], 'link_id' => $link['id'])) ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <?= Helper\form_hidden('id', $values) ?>
    <?= Helper\form_hidden('task_id', $values) ?>

    <?= Helper\form_label(t('Link Type'), 'link_id') ?>
    <?= Helper\form_select('link_id', $link_list, $values, $errors, 'required autofocus') ?><br/>

    <?= Helper\form_label(t('Linked Task'), 'task_inverse_id') ?>
    #<?= Helper\form_numeric('task_inverse_id', $values, $errors, array('required', 'placeholder="'.t('Task id').'"', 'list="task_inverse_ids"')) ?>
	<datalist id="task_inverse_ids">
	  <select>
        <?php foreach ($task_list as $task_inverse_id => $task_inverse_title): ?>
        <option value="<?= $task_inverse_id ?>">#<?= $task_inverse_id.' '.$task_inverse_title ?></option>
        <?php endforeach ?>
	  </select>
	</datalist>
	<br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= Helper\a(t('cancel'), 'task', 'show', array('task_id' => $task['id'])) ?>
    </div>
</form>
