<?php if (empty($users)): ?>
    <p><?= t('There is no project member.') ?></p>
<?php else: ?>
    <?php foreach ($roles as $role => $role_name): ?>
        <?php if (isset($users[$role])): ?>
        <strong><?= $role_name ?></strong>
        <ul>
            <?php foreach ($users[$role] as $user_id => $user): ?>
                <li><?= $this->url->link($this->text->e($user), 'Projectuser', 'opens', array('user_id' => $user_id)) ?></li>
            <?php endforeach ?>
        </ul>
        <?php endif ?>
    <?php endforeach ?>
<?php endif ?>