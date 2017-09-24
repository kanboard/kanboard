<div class="page-header">
    <h2><?= t('Choose an event') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('ActionCreationController', 'params', array('project_id' => $project['id'])) ?>">
    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('action_name', $values) ?>

    <?= $this->form->label(t('Action'), 'action_name') ?>
    <?= $this->form->select('action_name', $available_actions, $values, array(), array('disabled')) ?>

    <?= $this->form->label(t('Event'), 'event_name') ?>
    <?= $this->form->select('event_name', $events, $values) ?>

    <div class="form-help">
        <?= t('When the selected event occurs execute the corresponding action.') ?>
    </div>

    <?= $this->modal->submitButtons(array(
        'submitLabel' => t('Next step')
    )) ?>
</form>
