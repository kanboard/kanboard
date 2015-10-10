<div class="page-header">
    <h2><?= t('Webhook settings') ?></h2>
</div>
<section>
<form method="post" action="<?= $this->url->href('config', 'webhook') ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Webhook URL'), 'webhook_url') ?>
    <?= $this->form->text('webhook_url', $values, $errors) ?>

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
            <input type="text" class="auto-select" readonly="readonly" value="<?= $this->url->href('webhook', 'task', array('token' => $values['webhook_token']), false, '', true) ?>">
        </li>
        <li>
            <?= $this->url->link(t('Reset token'), 'config', 'token', array('type' => 'webhook'), true) ?>
        </li>
    </ul>
</section>