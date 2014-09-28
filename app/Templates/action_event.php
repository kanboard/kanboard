<div class="page-header">
    <h2><?= t('Automatic actions for the project "%s"', $project['name']) ?></h2>
</div>

<h3><?= t('Choose an event') ?></h3>
<form method="post" action="?controller=action&amp;action=params&amp;project_id=<?= $project['id'] ?>" autocomplete="off">
    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('project_id', $values) ?>
    <?= Helper\form_hidden('action_name', $values) ?>

    <?= Helper\form_label(t('Event'), 'event_name') ?>
    <?= Helper\form_select('event_name', $events, $values) ?><br/>

    <div class="form-help">
        <?= t('When the selected event occurs execute the corresponding action.') ?>
    </div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Next step') ?>" class="btn btn-blue"/>
        <?= t('or') ?> <a href="?controller=action&amp;action=index&amp;project_id=<?= $project['id'] ?>"><?= t('cancel') ?></a>
    </div>
</form>