<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong>#<?= $user['id'] ?> <i class="fa fa-caret-down"></i></strong></a>
    <ul>
        <li>
            <?= $this->url->icon('user', t('View profile'), 'UserViewController', 'show', array('user_id' => $user['id'])) ?>
        </li>
        <li>
            <?= $this->modal->medium('trash', t('Remove user from group'), 'GroupListController', 'dissociate', array('group_id' => $user['group_id'], 'user_id' => $user['id'])) ?>
        </li>
    </ul>
</div>
