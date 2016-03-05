<div class="page-header">
    <h2><?= t('Choose an event') ?></h2>
</div>

<form class="popover-form" method="post" action="<?= $this->url->href('ActionCreation', 'params', array('project_id' => $project['id'])) ?>">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('project_id', $values) ?>
    <?= $this->form->hidden('action_name', $values) ?>

    <?= $this->form->label(t('Action'), 'action_name') ?>
    <?= $this->form->select('action_name', $available_actions, $values, array(), array('disabled')) ?>

    <?= $this->form->label(t('Event'), 'event_name') ?>
    <?= $this->form->select('event_name', $events, $values) ?>

    <div class="form-help">
        <?= t('When the selected event occurs execute the corresponding action.') ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Next step') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'action', 'index', array('project_id' => $project['id']), false, 'close-popover') ?>
    </div>
</form>