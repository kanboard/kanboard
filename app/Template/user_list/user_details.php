<div class="table-list-details table-list-details-with-icons">
    <span class="table-list-category">
        <?= $this->user->getRoleName($user['role']) ?>
    </span>

    <?php if (! empty($user['name'])): ?>
        <span><?= $this->text->e($user['username']) ?></span>
    <?php endif ?>

    <?php if (! empty($user['email'])): ?>
        <span><a href="mailto:<?= $this->text->e($user['email']) ?>"><?= $this->text->e($user['email']) ?></a></span>
    <?php endif ?>

    <?php if ( SHOW_GROUP_MEMBERSHIPS_IN_USERLIST ): ?>
        <?php $groups_list_tooltip = t('%s is a member of the following group(s):', $user['name']) . '&#10;' . $this->user->getUsersGroupNames($user['id'])['full_list']; ?>
        <?php if ($this->user->getUsersGroupNames($user['id'])['has_groups']): ?>
            <span><i class="fa fa-fw fa-group aria-hidden="true" title="<?= $groups_list_tooltip ?>"></i> <?= $this->user->getUsersGroupNames($user['id'])['limited_list'] ?></span>
        <?php endif ?>
    <?php endif ?>
</div>
