<div class="page-header">
    <h2><?= t('Email notifications') ?></h2>
</div>

<form method="post" action="<?= Helper\u('user', 'notifications', array('user_id' => $user['id'])) ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <?= Helper\form_checkbox('notifications_enabled', t('Enable email notifications'), '1', $notifications['notifications_enabled'] == 1) ?><br/>

    <?php if (! empty($projects)): ?>
        <p><?= t('I want to receive notifications only for those projects:') ?><br/><br/></p>

        <div class="form-checkbox-group">
        <?php foreach ($projects as $project_id => $project_name): ?>
            <?= Helper\form_checkbox('projects['.$project_id.']', $project_name, '1', isset($notifications['project_'.$project_id])) ?><br/>
        <?php endforeach ?>
        </div>
    <?php endif ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= Helper\a(t('cancel'), 'user', 'show', array('user_id' => $user['id'])) ?>
    </div>
</form>