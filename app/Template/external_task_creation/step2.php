<form method="post" action="<?= $this->url->href('ExternalTaskCreationController', 'step3', array('project_id' => $project['id'], 'provider_name' => $provider_name)) ?>">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('external_provider', $values) ?>
    <?= $this->form->hidden('external_uri', $values) ?>

    <?= $this->render($template, array(
        'project' => $project,
        'external_task' => $external_task,
        'values' => $values,
        'errors' => $errors,
        'users_list' => $users_list,
        'categories_list' => $categories_list,
        'swimlanes_list' => $swimlanes_list,
        'columns_list' => $columns_list,
    )) ?>

    <?php if (! empty($error_message)): ?>
        <div class="alert alert-error"><?= $this->text->e($error_message) ?></div>
    <?php endif ?>

    <?= $this->modal->submitButtons() ?>
</form>
