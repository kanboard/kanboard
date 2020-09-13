<div class="table-list-icons">
    <?php if ($user['notifications_enabled'] == 1): ?>
        <span title="<?= t('Notifications are activated') ?>">
            <em class="fa fa-bell-o" aria-hidden="true"></em>
        </span>
    <?php endif ?>

    <?php if ($user['notifications_enabled'] == 0): ?>
        <span title="<?= t('Notifications are disabled') ?>">
            <em class="fa fa-bell-slash-o" aria-hidden="true"></em>
        </span>
    <?php endif ?>

    <?php if ($user['twofactor_activated'] == 1): ?>
        <span title="<?= t('Two factor authentication enabled') ?>">
            <em class="fa fa-shield" aria-hidden="true"></em>
        </span>
    <?php endif ?>

    <?php if ($user['is_ldap_user'] == 1): ?>
        <span title="<?= t('Remote user') ?>">
            <em class="fa fa-cloud" aria-hidden="true"></em>
        </span>
    <?php endif ?>

    <?php if ($user['lock_expiration_date'] != 0): ?>
        <span title="<?= t('Account locked until:') ?> <?= $this->dt->datetime($user['lock_expiration_date']) ?>">
            <em class="fa fa-lock" aria-hidden="true"></em>
        </span>
    <?php endif ?>

    <?php if ($user['role'] == 'app-admin'): ?>
        <span title="<?= $this->user->getRoleName($user['role']) ?>">
            <em class="fa fa-star" aria-hidden="true"></em>
        </span>
    <?php endif ?>

    <?php if ($user['role'] == 'app-manager'): ?>
        <span title="<?= $this->user->getRoleName($user['role']) ?>">
            <em class="fa fa-star-half-o" aria-hidden="true"></em>
        </span>
    <?php endif ?>

    <?php if ($user['role'] == 'app-user'): ?>
        <span title="<?= $this->user->getRoleName($user['role']) ?>">
            <em class="fa fa-star-o" aria-hidden="true"></em>
        </span>
    <?php endif ?>

    <?php if ($user['is_active'] == 0): ?>
        <span title="<?= t('User disabled') ?>">
            <em class="fa fa-ban" aria-hidden="true"></em>
        </span>
    <?php endif ?>
</div>