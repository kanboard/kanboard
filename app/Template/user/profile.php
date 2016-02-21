<section id="main">
    <br>
    <ul class="listing">
        <li><?= t('Username:') ?> <strong><?= $this->e($user['username']) ?></strong></li>
        <li><?= t('Name:') ?> <strong><?= $this->e($user['name']) ?: t('None') ?></strong></li>
        <li><?= t('Email:') ?> <strong><?= $this->e($user['email']) ?: t('None') ?></strong></li>
    </ul>
</section>