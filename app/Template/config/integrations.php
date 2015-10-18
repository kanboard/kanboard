<div class="page-header">
    <h2><?= t('Integration with third-party services') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('config', 'integrations') ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->hook->render('template:config:integrations', array('values' => $values)) ?>

    <h3><i class="fa fa-google"></i> <?= t('Google Authentication') ?></h3>
    <div class="listing">
    <input type="text" class="auto-select" readonly="readonly" value="<?= $this->url->href('oauth', 'google', array(), false, '', true) ?>"/><br/>
    <p class="form-help"><?= $this->url->doc(t('Help on Google authentication'), 'google-authentication') ?></p>
    </div>

    <h3><i class="fa fa-github"></i> <?= t('Github Authentication') ?></h3>
    <div class="listing">
    <input type="text" class="auto-select" readonly="readonly" value="<?= $this->url->href('oauth', 'github', array(), false, '', true) ?>"/><br/>
    <p class="form-help"><?= $this->url->doc(t('Help on Github authentication'), 'github-authentication') ?></p>
    </div>

    <h3><img src="<?= $this->url->dir() ?>assets/img/gitlab-icon.png"/>&nbsp;<?= t('Gitlab Authentication') ?></h3>
    <div class="listing">
    <input type="text" class="auto-select" readonly="readonly" value="<?= $this->url->href('oauth', 'gitlab', array(), false, '', true) ?>"/><br/>
    <p class="form-help"><?= $this->url->doc(t('Help on Gitlab authentication'), 'gitlab-authentication') ?></p>
    </div>

    <h3><img src="<?= $this->url->dir() ?>assets/img/gravatar-icon.png"/>&nbsp;<?= t('Gravatar') ?></h3>
    <div class="listing">
        <?= $this->form->checkbox('integration_gravatar', t('Enable Gravatar images'), 1, $values['integration_gravatar'] == 1) ?>
    </div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>