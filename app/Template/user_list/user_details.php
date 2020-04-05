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

    <?php if (SHOW_GROUP_MEMBERSHIPS_IN_USERLIST): ?>
        <?php $users_groups = $this->user->getUsersGroupNames($user['id']); ?>
        <?php $groups_list_tooltip = t('%s is a member of the following group(s): %s', $user['name'] ?: $user['username'], implode(', ', $users_groups['full_list'])); ?>
        <?php if ($users_groups['has_groups']): ?>
            <span title="<?= $groups_list_tooltip ?>">
                <i class="fa fa-fw fa-group" aria-hidden="true"></i><?= $this->text->implode(', ', $users_groups['limited_list']) ?>
                <?php if ($users_groups['shown'] != $users_groups['total']): ?>
                    ‑&nbsp;<?= t('%d/%d group(s) shown', $users_groups['shown'], $users_groups['total']) ?>
                <?php endif ?>
            </span>
        <?php endif ?>
    <?php endif ?>
</div>
