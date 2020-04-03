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

    <?php if ( $this->user->getDisplayGroupNamesInUserList($user['id']) ): ?>
        <?php $groups_list_tooltip = t('%s is a member of the following group(s):', $user['name']) . '&#10;' . $this->user->getGroupNames($user['id']); ?>
        <span><i class="fa fa-fw fa-group aria-hidden="true" title="<?= $groups_list_tooltip ?>"></i> <?= $this->user->getGroupNamesLimited($user['id']) ?></span>
    <?php endif ?>
</div>
