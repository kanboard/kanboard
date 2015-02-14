<div class="page-header">
    <h2><?= t('Add a new link') ?></h2>
</div>

<form action="<?= $this->u('tasklink', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" method="post" autocomplete="off">

    <?= $this->formCsrf() ?>
    <?= $this->formHidden('task_id', $values) ?>
    <?= $this->formHidden('opposite_task_id', $values) ?>

    <?= $this->formLabel(t('Label'), 'link_id') ?>
    <?= $this->formSelect('link_id', $labels, $values, $errors) ?>

    <?= $this->formLabel(t('Task'), 'title') ?>
    <?= $this->formText(
        'title',
        $values,
        $errors,
        array('required', 'data-dst-field="opposite_task_id"', 'data-search-url="'.$this->u('app', 'autocomplete', array('exclude_task_id' => $task['id'])).'"'),
        'task-autocomplete') ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    </div>
</form>