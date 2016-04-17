<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
    <ul>
        <li>
            <?= $this->url->button('fa-user', t('View profile'), 'user', 'show', array('user_id' => $user['id'])) ?>
        </li>
        <?php if ($user['is_active'] == 1 && $this->user->hasAccess('UserStatus', 'disable') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->url->button('fa-times', t('Disable'), 'UserStatus', 'confirmDisable', array('user_id' => $user['id']), false, 'popover') ?>
            </li>
        <?php endif ?>
        <?php if ($user['is_active'] == 0 && $this->user->hasAccess('UserStatus', 'enable') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->url->button('fa-check-square-o', t('Enable'), 'UserStatus', 'confirmEnable', array('user_id' => $user['id']), false, 'popover') ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->hasAccess('UserStatus', 'remove') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->url->button('fa-trash-o', t('Remove'), 'UserStatus', 'confirmRemove', array('user_id' => $user['id']), false, 'popover') ?>
            </li>
        <?php endif ?>
    </ul>
</div>
