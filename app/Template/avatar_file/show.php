<div class="page-header">
    <h2><?= t('Avatar') ?></h2>
</div>

<?= $this->avatar->render($user['id'], $user['username'], $user['name'], $user['email'], $user['avatar_path'], '') ?>

<form method="post" enctype="multipart/form-data" action="<?= $this->url->href('AvatarFile', 'upload', array('user_id' => $user['id'])) ?>">
    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Upload my avatar image'), 'avatar') ?>
    <?= $this->form->file('avatar') ?>

    <div class="form-actions">
        <?php if (! empty($user['avatar_path'])): ?>
            <?= $this->url->link(t('Remove my image'), 'AvatarFile', 'remove', array('user_id' => $user['id']), true, 'btn btn-red') ?>
        <?php endif ?>
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'user', 'show', array('user_id' => $user['id'])) ?>
    </div>
</form>
