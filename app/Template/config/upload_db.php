<div class="page-header">
    <h2><?= t('Upload the Sqlite database') ?></h2>
</div>

<div class="alert">
    <ul>
        <li><?= t('You can upload Gzip compressed Sqlite database you previously downloaded') ?></li>
    </ul>
</div>

<form action="<?= $this->url->href('ConfigController', 'uploadDbSave') ?>" method="post" enctype="multipart/form-data">
    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Database file'), 'file') ?>
    <?= $this->form->file('file') ?>

    <?= $this->modal->submitButtons(array('submitLabel' => t('Upload'))) ?>
</form>
