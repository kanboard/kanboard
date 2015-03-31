<div class="page-header">
    <h2><?= t('Edit a link') ?></h2>
</div>

<form action="<?= $this->u('tasklink', 'update', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" method="post" autocomplete="off">

    <?= $this->formCsrf() ?>
    <?= $this->formHidden('id', $values) ?>
    <?= $this->formHidden('task_id', array('task_id' => $task['id'])) ?>
    

    <?= $this->formLabel(t('Label'), 'link_id') ?>
    <?= $this->formSelect('link_id', $labels, $values, $errors) ?>

    <?= $this->formLabel(t('Task'), 'title') ?>
    <span class="opposite_task_id_bloc">
        #<?= $this->formNumeric('opposite_task_id', $values, $errors, array('required', 'placeholder="'.t('Task id').'"'), 'opposite_task_id') ?>
    </span>
    <?= $this->formText(
        'title',
        $values,
        $errors,
        array(
            'required',
            'style="display:none"',
            'placeholder="'.t('Start to type task title...').'"',
            'title="'.t('Start to type task title...').'"',
            'data-dst-field="opposite_task_id"',
            'data-search-url="'.$this->u('app', 'autocomplete', array('exclude_task_id' => $task['id'])).'"'
        ),
        'task-autocomplete') ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?php if (isset($ajax)): ?>
            <?= $this->a(t('cancel'), 'board', 'show', array('project_id' => $task['project_id'])) ?>
        <?php else: ?>
            <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        <?php endif ?>
    </div>
</form>