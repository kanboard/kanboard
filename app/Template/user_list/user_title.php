<div>
    <?= $this->render('user_list/dropdown', array('user' => $user)) ?>
    <span class="table-list-title <?= $user['is_active'] == 0 ? 'status-closed' : '' ?>">
        <?= $this->avatar->small(
            $user['id'],
            $user['username'],
            $user['name'],
            $user['email'],
            $user['avatar_path'],
            'avatar-inline'
        ) ?>
        <?= $this->url->link($this->text->e($user['name'] ?: $user['username']), 'UserViewController', 'show', array('user_id' => $user['id'])) ?>
    </span>
</div>
