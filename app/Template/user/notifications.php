<div class="page-header">
    <h2><?= t('Notifications') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('user', 'notifications', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->checkbox('notifications_enabled', t('Enable notifications'), '1', $notifications['notifications_enabled'] == 1) ?><br>

    <hr>
    <h4><?= t('Notification methods:') ?></h4>
    <?= $this->form->checkboxes('notification_types', $types, $notifications) ?>

    <hr>
    <h4><?= t('I want to receive notifications for:') ?></h4>
    <?= $this->form->radios('notifications_filter', $filters, $notifications) ?>

    <hr>
    <?php if (! empty($projects)): ?>
        <h4><?= t('I want to receive notifications only for those projects:') ?></h4>
        <?= $this->form->checkboxes('notification_projects', $projects, $notifications) ?>
    <?php endif ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'user', 'show', array('user_id' => $user['id'])) ?>
    </div>
</form>