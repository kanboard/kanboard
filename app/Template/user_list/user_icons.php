<div class="table-list-icons">
    <?php if ($user['notifications_enabled'] == 1): ?>
        <span title="<?= t('Notifications are activated') ?>">
            <i class="fa fa-bell-o" role="img" aria-label="<?= t('Notifications are activated') ?>"></i>
        </span>
    <?php endif ?>

    <?php if ($user['notifications_enabled'] == 0): ?>
        <span title="<?= t('Notifications are disabled') ?>">
            <i class="fa fa-bell-slash-o" role="img" aria-label="<?= t('Notifications are disabled') ?>"></i>
        </span>
    <?php endif ?>

    <?php if ($user['twofactor_activated'] == 1): ?>
        <span title="<?= t('Two factor authentication enabled') ?>">
            <i class="fa fa-shield" role="img" aria-label="<?= t('Two factor authentication enabled') ?>"></i>
        </span>
    <?php endif ?>

    <?php if ($user['is_ldap_user'] == 1): ?>
        <span title="<?= t('Remote user') ?>">
            <i class="fa fa-cloud" role="img" aria-label="<?= t('Remote user') ?>"></i>
        </span>
    <?php endif ?>

    <?php if ($user['lock_expiration_date'] != 0): ?>
        <?php $aria_label = t('Account locked until:') . ' ' . $this->dt->datetime($user['lock_expiration_date']); ?>
        <span title="<?= $aria_label ?>">
            <i class="fa fa-lock" role="img" aria-label="<?= $aria_label ?>"></i>
        </span>
    <?php endif ?>

    <?php if ($user['role'] == 'app-admin'): ?>
        <?php $aria_label = $this->user->getRoleName($user['role']); ?>
        <span title="<?= $aria_label ?>">
            <i class="fa fa-star" role="img" aria-label="<?= $aria_label ?>"></i>
        </span>
    <?php endif ?>

    <?php if ($user['role'] == 'app-manager'): ?>
        <?php $aria_label = $this->user->getRoleName($user['role']); ?>
        <span title="<?= $aria_label ?>">
            <i class="fa fa-star-half-o" role="img" aria-label="<?= $aria_label ?>"></i>
        </span>
    <?php endif ?>

    <?php if ($user['role'] == 'app-user'): ?>
        <?php $aria_label = $this->user->getRoleName($user['role']); ?>
        <span title="<?= $aria_label ?>">
            <i class="fa fa-star-o" role="img" aria-label="<?= $aria_label ?>"></i>
        </span>
    <?php endif ?>

    <?php if ($user['is_active'] == 0): ?>
        <span title="<?= t('User disabled') ?>">
            <i class="fa fa-ban" role="img" aria-label="<?= t('User disabled') ?>"></i>
        </span>
    <?php endif ?>
</div>