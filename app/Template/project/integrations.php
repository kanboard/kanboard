<div class="page-header">
    <h2><?= t('Integration with third-party services') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('project', 'integration', array('project_id' => $project['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>


    <h3><i class="fa fa-github fa-fw"></i>&nbsp;<?= t('Github webhooks') ?></h3>
    <div class="listing">
    <input type="text" class="auto-select" readonly="readonly" value="<?= $this->url->href('webhook', 'github', array('token' => $webhook_token, 'project_id' => $project['id']), false, '', true) ?>"/><br/>
    <p class="form-help"><?= $this->url->doc(t('Help on Github webhooks'), 'github-webhooks') ?></p>
    </div>


    <h3><img src="<?= $this->url->dir() ?>assets/img/gitlab-icon.png"/>&nbsp;<?= t('Gitlab webhooks') ?></h3>
    <div class="listing">
    <input type="text" class="auto-select" readonly="readonly" value="<?= $this->url->href('webhook', 'gitlab', array('token' => $webhook_token, 'project_id' => $project['id']), false, '', true) ?>"/><br/>
    <p class="form-help"><?= $this->url->doc(t('Help on Gitlab webhooks'), 'gitlab-webhooks') ?></p>
    </div>


    <h3><i class="fa fa-bitbucket fa-fw"></i>&nbsp;<?= t('Bitbucket webhooks') ?></h3>
    <div class="listing">
    <input type="text" class="auto-select" readonly="readonly" value="<?= $this->url->href('webhook', 'bitbucket', array('token' => $webhook_token, 'project_id' => $project['id']), false, '', true) ?>"/><br/>
    <p class="form-help"><?= $this->url->doc(t('Help on Bitbucket webhooks'), 'bitbucket-webhooks') ?></p>
    </div>


    <h3><img src="<?= $this->url->dir() ?>assets/img/jabber-icon.png"/> <?= t('Jabber (XMPP)') ?></h3>
    <div class="listing">
        <?= $this->form->checkbox('jabber', t('Send notifications to Jabber'), 1, isset($values['jabber']) && $values['jabber'] == 1) ?>

        <?= $this->form->label(t('XMPP server address'), 'jabber_server') ?>
        <?= $this->form->text('jabber_server', $values, $errors, array('placeholder="tcp://myserver:5222"')) ?>
        <p class="form-help"><?= t('The server address must use this format: "tcp://hostname:5222"') ?></p>

        <?= $this->form->label(t('Jabber domain'), 'jabber_domain') ?>
        <?= $this->form->text('jabber_domain', $values, $errors, array('placeholder="example.com"')) ?>

        <?= $this->form->label(t('Username'), 'jabber_username') ?>
        <?= $this->form->text('jabber_username', $values, $errors) ?>

        <?= $this->form->label(t('Password'), 'jabber_password') ?>
        <?= $this->form->password('jabber_password', $values, $errors) ?>

        <?= $this->form->label(t('Jabber nickname'), 'jabber_nickname') ?>
        <?= $this->form->text('jabber_nickname', $values, $errors) ?>

        <?= $this->form->label(t('Multi-user chat room'), 'jabber_room') ?>
        <?= $this->form->text('jabber_room', $values, $errors, array('placeholder="myroom@conference.example.com"')) ?>

        <p class="form-help"><?= $this->url->doc(t('Help on Jabber integration'), 'jabber') ?></p>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        </div>
    </div>


    <h3><img src="<?= $this->url->dir() ?>assets/img/hipchat-icon.png"/> <?= t('Hipchat') ?></h3>
    <div class="listing">
        <?= $this->form->checkbox('hipchat', t('Send notifications to Hipchat'), 1, isset($values['hipchat']) && $values['hipchat'] == 1) ?>

        <?= $this->form->label(t('API URL'), 'hipchat_api_url') ?>
        <?= $this->form->text('hipchat_api_url', $values, $errors) ?>

        <?= $this->form->label(t('Room API ID or name'), 'hipchat_room_id') ?>
        <?= $this->form->text('hipchat_room_id', $values, $errors) ?>

        <?= $this->form->label(t('Room notification token'), 'hipchat_room_token') ?>
        <?= $this->form->text('hipchat_room_token', $values, $errors) ?>

        <p class="form-help"><?= $this->url->doc(t('Help on Hipchat integration'), 'hipchat') ?></a></p>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        </div>
    </div>


    <h3><i class="fa fa-slack fa-fw"></i>&nbsp;<?= t('Slack') ?></h3>
    <div class="listing">
        <?= $this->form->checkbox('slack', t('Send notifications to a Slack channel'), 1, isset($values['slack']) && $values['slack'] == 1) ?>

        <?= $this->form->label(t('Webhook URL'), 'slack_webhook_url') ?>
        <?= $this->form->text('slack_webhook_url', $values, $errors) ?>
        <?= $this->form->label(t('Channel/Group/User (Optional)'), 'slack_webhook_channel') ?>
        <?= $this->form->text('slack_webhook_channel', $values, $errors) ?>

        <p class="form-help"><?= $this->url->doc(t('Help on Slack integration'), 'slack') ?></p>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        </div>
    </div>
</form>