<div class="page-header">
    <h2><?= t('Avatar') ?></h2>
</div>

<?= $this->avatar->render($user['id'], $user['username'], $user['name'], $user['email'], $user['avatar_path'], '') ?>

<div class="form-actions">
<?php if (! empty($user['avatar_path'])): ?>
    <?= $this->url->link(t('Remove my image'), 'AvatarFileController', 'remove', array('user_id' => $user['id']), true, 'btn btn-red js-modal-replace') ?>
<?php endif ?>
</div>

<hr>

<h3><?= t('Upload my avatar image') ?></h3>
<form method="post" enctype="multipart/form-data" action="<?= $this->url->href('AvatarFileController', 'upload', array('user_id' => $user['id'])) ?>">
    <?= $this->form->csrf() ?>
    <?= $this->form->file('avatar') ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Upload my avatar image') ?></button>
    </div>
</form>
