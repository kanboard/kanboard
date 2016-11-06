<form class="popover-form" method="post" action="<?= $this->url->href('ExternalTaskCreationController', 'step2', array('project_id' => $project['id'], 'provider_name' => $provider_name)) ?>">
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

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Next') ?></button>
        <?= t('or') ?> <?= $this->url->link(t('cancel'), 'BoardViewController', 'show', array('project_id' => $project['id']), false, 'close-popover') ?>
    </div>
</form>
