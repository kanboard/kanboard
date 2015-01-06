<div class="page-header">
    <h2><?= t('Duplicate the task to another project') ?></h2>
</div>

<?php if (empty($projects_list)): ?>
    <p class="alert"><?= t('No project') ?></p>
<?php else: ?>

    <form method="post" action="<?= $this->u('task', 'copy', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">

        <?= $this->formCsrf() ?>

        <?= $this->formHidden('id', $values) ?>
        <?= $this->formLabel(t('Project'), 'project_id') ?>
        <?= $this->formSelect('project_id', $projects_list, $values, $errors) ?><br/>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?>
            <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </div>
    </form>

<?php endif ?>