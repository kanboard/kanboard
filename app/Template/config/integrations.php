<div class="page-header">
    <h2><?= t('Integration with third-party services') ?></h2>
</div>

<form method="post" action="<?= $this->u('config', 'integrations') ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <h3><?= t('Gravatar') ?></h3>
    <div class="listing">
        <?= $this->formCheckbox('integration_gravatar', t('Enable Gravatar images'), 1, $values['integration_gravatar'] == 1) ?>
    </div>

    <h3><img src="assets/img/hipchat-icon.png"/> <?= t('Hipchat') ?></h3>
    <div class="listing">
        <?= $this->formCheckbox('integration_hipchat', t('Send notifications to Hipchat'), 1, $values['integration_hipchat'] == 1) ?>

        <?= $this->formLabel(t('API URL'), 'integration_hipchat_api_url') ?>
        <?= $this->formText('integration_hipchat_api_url', $values, $errors) ?>

        <?= $this->formLabel(t('Room API ID or name'), 'integration_hipchat_room_id') ?>
        <?= $this->formText('integration_hipchat_room_id', $values, $errors) ?>

        <?= $this->formLabel(t('Room notification token'), 'integration_hipchat_room_token') ?>
        <?= $this->formText('integration_hipchat_room_token', $values, $errors) ?>

        <p class="form-help"><a href="http://kanboard.net/documentation/hipchat" target="_blank"><?= t('Help on Hipchat integration') ?></a></p>
    </div>

    <h3><i class="fa fa-slack fa-fw"></i>&nbsp;<?= t('Slack') ?></h3>
    <div class="listing">
        <?= $this->formCheckbox('integration_slack_webhook', t('Send notifications to a Slack channel'), 1, $values['integration_slack_webhook'] == 1) ?>

        <?= $this->formLabel(t('Webhook URL'), 'integration_slack_webhook_url') ?>
        <?= $this->formText('integration_slack_webhook_url', $values, $errors) ?>

        <p class="form-help"><a href="http://kanboard.net/documentation/slack" target="_blank"><?= t('Help on Slack integration') ?></a></p>
    </div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>