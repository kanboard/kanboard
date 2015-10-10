<div class="page-header">
    <h2><?= t('External authentications') ?></h2>
</div>

<?php if (GOOGLE_AUTH): ?>
    <h3><i class="fa fa-google"></i> <?= t('Google Account') ?></h3>

    <p class="listing">
    <?php if ($this->user->isCurrentUser($user['id'])): ?>
        <?php if (empty($user['google_id'])): ?>
            <?= $this->url->link(t('Link my Google Account'), 'oauth', 'google', array(), true) ?>
        <?php else: ?>
            <?= $this->url->link(t('Unlink my Google Account'), 'oauth', 'unlink', array('backend' => 'google'), true) ?>
        <?php endif ?>
    <?php else: ?>
        <?= empty($user['google_id']) ? t('No account linked.') : t('Account linked.') ?>
    <?php endif ?>
    </p>
<?php endif ?>

<?php if (GITHUB_AUTH): ?>
    <h3><i class="fa fa-github"></i> <?= t('Github Account') ?></h3>

    <p class="listing">
    <?php if ($this->user->isCurrentUser($user['id'])): ?>
        <?php if (empty($user['github_id'])): ?>
            <?= $this->url->link(t('Link my Github Account'), 'oauth', 'github', array(), true) ?>
        <?php else: ?>
            <?= $this->url->link(t('Unlink my Github Account'), 'oauth', 'unlink', array('backend' => 'github'), true) ?>
        <?php endif ?>
    <?php else: ?>
        <?= empty($user['github_id']) ? t('No account linked.') : t('Account linked.') ?>
    <?php endif ?>
    </p>
<?php endif ?>

<?php if (GITLAB_AUTH): ?>
    <h3><img src="<?= $this->url->dir() ?>assets/img/gitlab-icon.png"/>&nbsp;<?= t('Gitlab Account') ?></h3>

    <p class="listing">
    <?php if ($this->user->isCurrentUser($user['id'])): ?>
        <?php if (empty($user['gitlab_id'])): ?>
            <?= $this->url->link(t('Link my Gitlab Account'), 'oauth', 'gitlab', array(), true) ?>
        <?php else: ?>
            <?= $this->url->link(t('Unlink my Gitlab Account'), 'oauth', 'unlink', array('backend' => 'gitlab'), true) ?>
        <?php endif ?>
    <?php else: ?>
        <?= empty($user['gitlab_id']) ? t('No account linked.') : t('Account linked.') ?>
    <?php endif ?>
    </p>
<?php endif ?>

<?php if (! GOOGLE_AUTH && ! GITHUB_AUTH && ! GITLAB_AUTH): ?>
    <p class="alert"><?= t('No external authentication enabled.') ?></p>
<?php endif ?>
