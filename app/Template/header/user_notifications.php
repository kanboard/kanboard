<?php if ($this->user->hasNotifications()): ?>
    <span class="notification">
        <?= $this->url->link('<i class="fa fa-bell web-notification-icon"></i>', 'DashboardController', 'notifications', array('user_id' => $this->user->getId()), false, '', t('Unread notifications')) ?>
    </span>
<?php endif ?>
