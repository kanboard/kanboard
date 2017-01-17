<div class="page-header">
    <h2><?= t('Import users from CSV file') ?></h2>
    <ul>
        <li>
            <?= $this->url->icon('download', t('Download CSV template'), 'UserImportController', 'template') ?>
        </li>
    </ul>
</div>

<div class="alert">
    <ul>
        <li><?= t('Your file must use the predefined CSV format') ?></li>
        <li><?= t('Your file must be encoded in UTF-8') ?></li>
        <li><?= t('The first row must be the header') ?></li>
        <li><?= t('Duplicates are not imported') ?></li>
        <li><?= t('Usernames must be lowercase and unique') ?></li>
        <li><?= t('Passwords will be encrypted if present') ?></li>
    </ul>
</div>

<form action="<?= $this->url->href('UserImportController', 'save') ?>" method="post" enctype="multipart/form-data">
    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Delimiter'), 'delimiter') ?>
    <?= $this->form->select('delimiter', $delimiters, $values) ?>

    <?= $this->form->label(t('Enclosure'), 'enclosure') ?>
    <?= $this->form->select('enclosure', $enclosures, $values) ?>

    <?= $this->form->label(t('CSV File'), 'file') ?>
    <?= $this->form->file('file', $errors) ?>

    <p class="form-help"><?= t('Maximum size: ') ?><?= is_integer($max_size) ? $this->text->bytes($max_size) : $max_size ?></p>

    <?= $this->modal->submitButtons(array('submitLabel' => t('Import'))) ?>
</form>
