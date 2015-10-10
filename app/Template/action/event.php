<div class="page-header">
    <h2><?= t('Automatic actions for the project "%s"', $project['name']) ?></h2>
</div>

<h3><?= t('Choose an event') ?></h3>
<form method="post" action="<?= $this->url->href('action', 'params', array('project_id' => $project['id'])) ?>">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('project_id', $values) ?>
    <?= $this->form->hidden('action_name', $values) ?>

    <?= $this->form->label(t('Event'), 'event_name') ?>
    <?= $this->form->select('event_name', $events, $values) ?><br/>

    <div class="form-help">
        <?= t('When the selected event occurs execute the corresponding action.') ?>
    </div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Next step') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'action', 'index', array('project_id' => $project['id'])) ?>
    </div>
</form>