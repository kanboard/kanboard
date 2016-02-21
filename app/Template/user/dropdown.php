<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
    <ul>
        <li>
            <i class="fa fa-user fa-fw"></i>
            <?= $this->url->link(t('View profile'), 'user', 'show', array('user_id' => $user['id'])) ?>
        </li>
        <?php if ($user['is_active'] == 1 && $this->user->hasAccess('UserStatus', 'disable') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <i class="fa fa-times fa-fw"></i>
                <?= $this->url->link(t('Disable'), 'UserStatus', 'confirmDisable', array('user_id' => $user['id']), false, 'popover') ?>
            </li>
        <?php endif ?>
        <?php if ($user['is_active'] == 0 && $this->user->hasAccess('UserStatus', 'enable') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <i class="fa fa-check-square-o fa-fw"></i>
                <?= $this->url->link(t('Enable'), 'UserStatus', 'confirmEnable', array('user_id' => $user['id']), false, 'popover') ?>
            </li>
        <?php endif ?>
        <?php if ($this->user->hasAccess('UserStatus', 'remove') && ! $this->user->isCurrentUser($user['id'])): ?>
            <li>
                <i class="fa fa-trash-o fa-fw"></i>
                <?= $this->url->link(t('Remove'), 'UserStatus', 'confirmRemove', array('user_id' => $user['id']), false, 'popover') ?>
            </li>
        <?php endif ?>
    </ul>
</div>