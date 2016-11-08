<form class="popover-form" method="post" action="<?= $this->url->href('TaskModificationController', 'update', array('task_id' => $task['id'], 'project_id' => $project['id'])) ?>">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?php if (! empty($error_message)): ?>
        <p class="alert alert-error"><?= $this->text->e($error_message) ?></p>
    <?php else: ?>
        <?= $this->render($template, array(
            'project' => $project,
            'task' => $task,
            'external_task' => $external_task,
            'tags' => $tags,
            'users_list' => $users_list,
            'categories_list' => $categories_list,
            'values' => $values,
            'errors' => $errors,
        )) ?>
    <?php endif ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover') ?>
    </div>
</form>
