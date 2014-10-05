<div class="page-header">
    <h2><?= t('Webhook settings') ?></h2>
</div>
<section>
<form method="post" action="<?= Helper\u('config', 'webhook') ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <?= Helper\form_label(t('Webhook URL for task creation'), 'webhook_url_task_creation') ?>
    <?= Helper\form_text('webhook_url_task_creation', $values, $errors) ?><br/>

    <?= Helper\form_label(t('Webhook URL for task modification'), 'webhook_url_task_modification') ?>
    <?= Helper\form_text('webhook_url_task_modification', $values, $errors) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>

<div class="page-header">
    <h2><?= t('URL and token') ?></h2>
</div>
<section class="listing">
    <ul>
        <li>
            <?= t('Webhook token:') ?>
            <strong><?= Helper\escape($values['webhook_token']) ?></strong>
        </li>
        <li>
            <?= t('URL for task creation:') ?>
            <input type="text" readonly="readonly" value="<?= Helper\get_current_base_url().Helper\u('webhook', 'task', array('token' => $values['webhook_token'])) ?>">
        </li>
        <li>
            <?= Helper\a(t('Reset token'), 'config', 'token', array('type' => 'webhook'), true) ?>
        </li>
    </ul>
</section>