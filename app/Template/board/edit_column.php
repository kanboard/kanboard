<div class="page-header">
    <h2><?= t('Edit column "%s"', $column['title']) ?></h2>
</div>

<form method="post" action="<?= $this->u('board', 'updateColumn', array('project_id' => $project['id'], 'column_id' => $column['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formHidden('id', $values) ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Title'), 'title') ?>
    <?= $this->formText('title', $values, $errors, array('autofocus', 'required', 'maxlength="50"')) ?>

    <?= $this->formLabel(t('Task limit'), 'task_limit') ?>
    <?= $this->formNumber('task_limit', $values, $errors) ?>

    <?= $this->formLabel(t('Description'), 'description') ?>
    <?= $this->formTextarea('description', $values, $errors) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>