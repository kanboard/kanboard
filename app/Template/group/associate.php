<section id="main">
    <div class="page-header">
        <ul class="btn-group">
            <li><?= $this->url->button('users', t('View all groups'), 'group', 'index') ?></li>
            <li><?= $this->url->button('user', t('View group members'), 'group', 'users', array('group_id' => $group['id'])) ?></li>
        </ul>
    </div>
    <?php if (empty($users)): ?>
        <p class="alert"><?= t('There is no user available.') ?></p>
    <?php else: ?>
        <form method="post" action="<?= $this->url->href('group', 'addUser', array('group_id' => $group['id'])) ?>" autocomplete="off">
            <?= $this->form->csrf() ?>
            <?= $this->form->hidden('group_id', $values) ?>

            <?= $this->form->label(t('User'), 'user_id') ?>
            <?= $this->form->select('user_id', $users, $values, $errors, array('required'), 'chosen-select') ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
                <?= t('or') ?>
                <?= $this->url->link(t('cancel'), 'group', 'index') ?>
            </div>
        </form>
    <?php endif ?>
</section>
