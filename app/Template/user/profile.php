<section id="main">
    <br>
    <?= $this->avatar->render($user['id'], $user['username'], $user['name'], $user['email'], $user['avatar_path']) ?>
    <ul class="listing">
        <li><?= t('Username:') ?> <strong><?= $this->text->e($user['username']) ?></strong></li>
        <li><?= t('Name:') ?> <strong><?= $this->text->e($user['name']) ?: t('None') ?></strong></li>
        <li><?= t('Email:') ?> <strong><?= $this->text->e($user['email']) ?: t('None') ?></strong></li>
    </ul>
</section>