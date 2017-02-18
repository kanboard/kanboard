<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong><?= '#'.$user['id'] ?> <i class="fa fa-caret-down"></i></strong></a>
    <ul>
        <li>
            <?= $this->url->icon('user', t('View profile'), 'UserViewController', 'show', array('user_id' => $user['id'])) ?>
        </li>
        <?php if ($user['is_active'] == 1 && $this->user->hasAccess('UserStatusController', 'disable') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->modal->confirm('times', t('Disable'), 'UserStatusController', 'confirmDisable', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($user['is_active'] == 0 && $this->user->hasAccess('UserStatusController', 'enable') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->modal->confirm('check-square-o', t('Enable'), 'UserStatusController', 'confirmEnable', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->hasAccess('UserStatusController', 'remove') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->modal->confirm('trash-o', t('Remove'), 'UserStatusController', 'confirmRemove', array('user_id' => $user['id'])) ?>
            </li>
        <?php endif ?>
    </ul>
</div>
