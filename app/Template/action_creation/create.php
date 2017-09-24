<div class="page-header">
    <h2><?= t('Add an action') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('ActionCreationController', 'event', array('project_id' => $project['id'])) ?>">
    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Action'), 'action_name') ?>
    <?= $this->form->select('action_name', $available_actions, $values) ?>

    <?= $this->modal->submitButtons(array(
        'submitLabel' => t('Next step')
    )) ?>
</form>
