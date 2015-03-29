<div class="page-header">
    <h2><?= t('Integration with third-party services') ?></h2>
</div>

<form method="post" action="<?= $this->u('config', 'integrations') ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

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