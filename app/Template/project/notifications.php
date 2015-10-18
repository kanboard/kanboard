<div class="page-header">
    <h2><?= t('Notifications') ?></h2>
</div>
<?php if (empty($types)): ?>
    <p class="alert"><?= t('There is no notification method registered.') ?></p>
<?php else: ?>
    <form method="post" action="<?= $this->url->href('project', 'notifications', array('project_id' => $project['id'])) ?>" autocomplete="off">

        <?= $this->form->csrf() ?>

        <h4><?= t('Notification methods:') ?></h4>
        <?= $this->form->checkboxes('notification_types', $types, $notifications) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'project', 'show', array('project_id' => $project['id'])) ?>
        </div>
    </form>
<?php endif ?>