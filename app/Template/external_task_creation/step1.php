<form method="post" action="<?= $this->url->href('ExternalTaskCreationController', 'step2', array('project_id' => $project['id'], 'provider_name' => $provider_name)) ?>">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('swimlane_id', $values) ?>
    <?= $this->form->hidden('column_id', $values) ?>

    <?= $this->render($template, array(
        'project' => $project,
        'values' => $values,
    )) ?>

    <?php if (! empty($error_message)): ?>
        <div class="alert alert-error"><?= $this->text->e($error_message) ?></div>
    <?php endif ?>

    <?= $this->modal->submitButtons(array('submitLabel' => t('Next'))) ?>
</form>
