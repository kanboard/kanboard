<div class="page-header">
    <h2><?= t('Move the task to another project') ?></h2>
</div>

<form method="post" action="?controller=task&amp;action=move&amp;task_id=<?= $task['id'] ?>&amp;project_id=<?= $task['project_id'] ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <?= Helper\form_hidden('id', $values) ?>
    <?= Helper\form_label(t('Project'), 'project_id') ?>
    <?= Helper\form_select('project_id', $projects_list, $values, $errors) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <a href="?controller=board&amp;action=show&amp;project_id=<?= $task['project_id'] ?>"><?= t('cancel') ?></a>
    </div>
</form>