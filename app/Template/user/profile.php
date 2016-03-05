<section id="main">
    <br>
    <ul class="listing">
        <li><?= t('Username:') ?> <strong><?= $this->text->e($user['username']) ?></strong></li>
        <li><?= t('Name:') ?> <strong><?= $this->text->e($user['name']) ?: t('None') ?></strong></li>
        <li><?= t('Email:') ?> <strong><?= $this->text->e($user['email']) ?: t('None') ?></strong></li>
    </ul>
</section>