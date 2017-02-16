<div class="page-header">
    <h2><?= t('Upload the Sqlite database') ?></h2>
</div>

<div class="alert">
    <p>
        <?= t('You can upload the Gzip compressed Sqlite database previously downloaded.') ?>
    </p>
</div>

<form action="<?= $this->url->href('ConfigController', 'saveUploadedDb') ?>" method="post" enctype="multipart/form-data">
    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Database file'), 'file') ?>
    <?= $this->form->file('file') ?>

    <?= $this->modal->submitButtons(array('submitLabel' => t('Upload'))) ?>
</form>
