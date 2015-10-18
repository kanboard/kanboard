<div class="page-header">
    <h2><?= t('Integration with third-party services') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('project', 'integrations', array('project_id' => $project['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <?= $this->hook->render('template:project:integrations', array('values' => $values)) ?>

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
</form>