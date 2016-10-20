<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
    <ul>
        <li>
            <?= $this->url->link('<i class="fa fa-user fa-fw"></i>' . t('View profile'), 'UserViewController', 'show', array('user_id' => $user['id'])) ?>
        </li>
        <?php if ($user['is_active'] == 1 && $this->user->hasAccess('UserStatusController', 'disable') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-times fa-fw"></i>' . t('Disable'), 'UserStatusController', 'confirmDisable', array('user_id' => $user['id']), false, 'popover') ?>
            </li>
        <?php endif ?>
        <?php if ($user['is_active'] == 0 && $this->user->hasAccess('UserStatusController', 'enable') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-check-square-o fa-fw"></i>' . t('Enable'), 'UserStatusController', 'confirmEnable', array('user_id' => $user['id']), false, 'popover') ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->hasAccess('UserStatusController', 'remove') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->url->link('<i class="fa fa-trash-o fa-fw"></i>' . t('Remove'), 'UserStatusController', 'confirmRemove', array('user_id' => $user['id']), false, 'popover') ?>
            </li>
        <?php endif ?>
    </ul>
</div>
