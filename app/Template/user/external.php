<div class="page-header">
    <h2><?= t('External authentications') ?></h2>
</div>

<?php if (GOOGLE_AUTH): ?>
    <h3><i class="fa fa-google"></i> <?= t('Google Account') ?></h3>

    <p class="listing">
    <?php if (Helper\is_current_user($user['id'])): ?>
        <?php if (empty($user['google_id'])): ?>
            <?= Helper\a(t('Link my Google Account'), 'user', 'google', array(), true) ?>
        <?php else: ?>
            <?= Helper\a(t('Unlink my Google Account'), 'user', 'unlinkGoogle', array(), true) ?>
        <?php endif ?>
    <?php else: ?>
        <?= empty($user['google_id']) ? t('No account linked.') : t('Account linked.') ?>
    <?php endif ?>
    </p>
<?php endif ?>

<?php if (GITHUB_AUTH): ?>
    <h3><i class="fa fa-github"></i> <?= t('Github Account') ?></h3>

    <p class="listing">
    <?php if (Helper\is_current_user($user['id'])): ?>
        <?php if (empty($user['github_id'])): ?>
            <?= Helper\a(t('Link my GitHub Account'), 'user', 'github', array(), true) ?>
        <?php else: ?>
            <?= Helper\a(t('Unlink my GitHub Account'), 'user', 'unlinkGitHub', array(), true) ?>
        <?php endif ?>
    <?php else: ?>
        <?= empty($user['github_id']) ? t('No account linked.') : t('Account linked.') ?>
    <?php endif ?>
    </p>
<?php endif ?>

<?php if (! GOOGLE_AUTH && ! GITHUB_AUTH): ?>
    <p class="alert"><?= t('No external authentication enabled.') ?></p>
<?php endif ?>
