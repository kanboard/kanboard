<div class="page-header">
    <h2><?= t('External authentications') ?></h2>
</div>

<?php if (GOOGLE_AUTH): ?>
    <h3><?= t('Google Account') ?></h3>

    <p class="settings">
    <?php if (Helper\is_current_user($user['id'])): ?>
        <?php if (empty($user['google_id'])): ?>
            <a href="?controller=user&amp;action=google<?= Helper\param_csrf() ?>"><?= t('Link my Google Account') ?></a>
        <?php else: ?>
            <a href="?controller=user&amp;action=unlinkGoogle<?= Helper\param_csrf() ?>"><?= t('Unlink my Google Account') ?></a>
        <?php endif ?>
    <?php else: ?>
        <?= empty($user['google_id']) ? t('No account linked.') : t('Account linked.') ?>
    <?php endif ?>
    </p>
<?php endif ?>

<?php if (GITHUB_AUTH): ?>
    <h3><?= t('Github Account') ?></h3>

    <p class="settings">
    <?php if (Helper\is_current_user($user['id'])): ?>
        <?php if (empty($user['github_id'])): ?>
            <a href="?controller=user&amp;action=gitHub<?= Helper\param_csrf() ?>"><?= t('Link my GitHub Account') ?></a>
        <?php else: ?>
            <a href="?controller=user&amp;action=unlinkGitHub<?= Helper\param_csrf() ?>"><?= t('Unlink my GitHub Account') ?></a>
        <?php endif ?>
    <?php else: ?>
        <?= empty($user['github_id']) ? t('No account linked.') : t('Account linked.') ?>
    <?php endif ?>
    </p>
<?php endif ?>

<?php if (! GOOGLE_AUTH && ! GITHUB_AUTH): ?>
    <p class="alert"><?= t('No external authentication enabled.') ?></p>
<?php endif ?>
