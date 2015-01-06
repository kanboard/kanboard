<div class="page-header">
    <h2><?= t('Integration with third-party services') ?></h2>
</div>

<h3><i class="fa fa-github fa-fw"></i>&nbsp;<?= t('Github webhooks') ?></h3>
<div class="listing">
<input type="text" class="auto-select" readonly="readonly" value="<?= $this->getCurrentBaseUrl().$this->u('webhook', 'github', array('token' => $webhook_token, 'project_id' => $project['id'])) ?>"/><br/>
<p class="form-help"><a href="http://kanboard.net/documentation/github-webhooks" target="_blank"><?= t('Help on Github webhooks') ?></a></p>
</div>

<h3><i class="fa fa-git fa-fw"></i>&nbsp;<?= t('Gitlab webhooks') ?></h3>
<div class="listing">
<input type="text" class="auto-select" readonly="readonly" value="<?= $this->getCurrentBaseUrl().$this->u('webhook', 'gitlab', array('token' => $webhook_token, 'project_id' => $project['id'])) ?>"/><br/>
<p class="form-help"><a href="http://kanboard.net/documentation/gitlab-webhooks" target="_blank"><?= t('Help on Gitlab webhooks') ?></a></p>
</div>