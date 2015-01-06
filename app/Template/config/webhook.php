<div class="page-header">
    <h2><?= t('Webhook settings') ?></h2>
</div>
<section>
<form method="post" action="<?= $this->u('config', 'webhook') ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formLabel(t('Webhook URL for task creation'), 'webhook_url_task_creation') ?>
    <?= $this->formText('webhook_url_task_creation', $values, $errors) ?><br/>

    <?= $this->formLabel(t('Webhook URL for task modification'), 'webhook_url_task_modification') ?>
    <?= $this->formText('webhook_url_task_modification', $values, $errors) ?><br/>

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
            <strong><?= $this->e($values['webhook_token']) ?></strong>
        </li>
        <li>
            <?= t('URL for task creation:') ?>
            <input type="text" class="auto-select" readonly="readonly" value="<?= $this->getCurrentBaseUrl().$this->u('webhook', 'task', array('token' => $values['webhook_token'])) ?>">
        </li>
        <li>
            <?= $this->a(t('Reset token'), 'config', 'token', array('type' => 'webhook'), true) ?>
        </li>
    </ul>
</section>