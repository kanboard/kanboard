<div class="page-header">
    <h2><?= t('Add a sub-task') ?></h2>
</div>

<form method="post" action="?controller=subtask&amp;action=save&amp;task_id=<?= $task['id'] ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <?= Helper\form_hidden('task_id', $values) ?>

    <?= Helper\form_label(t('Title'), 'title') ?>
    <?= Helper\form_text('title', $values, $errors, array('required autofocus')) ?><br/>

    <?= Helper\form_label(t('Assignee'), 'user_id') ?>
    <?= Helper\form_select('user_id', $users_list, $values, $errors) ?><br/>

    <?= Helper\form_label(t('Original estimate'), 'time_estimated') ?>
    <?= Helper\form_numeric('time_estimated', $values, $errors) ?> <?= t('hours') ?><br/>

    <?= Helper\form_checkbox('another_subtask', t('Create another sub-task'), 1, isset($values['another_subtask']) && $values['another_subtask'] == 1) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>"><?= t('cancel') ?></a>
    </div>
</form>
