<div class="page-header">
    <h2><?= t('Move the task to another project') ?></h2>
</div>

<?php if (empty($projects_list)): ?>
    <p class="alert"><?= t('No project') ?></p>
<?php else: ?>

    <form method="post" action="<?= Helper\u('task', 'move', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">

        <?= Helper\form_csrf() ?>

        <?= Helper\form_hidden('id', $values) ?>
        <?= Helper\form_label(t('Project'), 'project_id') ?>
        <?= Helper\form_select('project_id', $projects_list, $values, $errors) ?><br/>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?>
            <?= Helper\a(t('cancel'), 'task', 'show', array('task_id' => $task['id'])) ?>
        </div>
    </form>

<?php endif ?>