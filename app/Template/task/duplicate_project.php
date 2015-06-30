<div class="page-header">
    <h2><?= t('Duplicate the task to another project') ?></h2>
</div>

<?php if (empty($projects_list)): ?>
    <p class="alert"><?= t('No project') ?></p>
<?php else: ?>

    <form method="post" action="<?= $this->url->href('task', 'copy', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">

        <?= $this->form->csrf() ?>

        <?= $this->form->hidden('id', $values) ?>
        <?= $this->form->label(t('Project'), 'project_id') ?>
        <?= $this->form->select('project_id', $projects_list, $values, $errors) ?><br/>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </div>
    </form>

<?php endif ?>