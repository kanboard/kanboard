<div class="page-header">
    <h2><?= t('Tasks Importation') ?></h2>
</div>
<form action="<?= $this->url->href('TaskImportController', 'save', array('project_id' => $project['id'])) ?>" method="post" enctype="multipart/form-data">
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

<div class="panel">
    <h3><?= t('Instructions') ?></h3>
    <ul>
        <li><?= t('Your file must use the predefined CSV format') ?></li>
        <li><?= t('Your file must be encoded in UTF-8') ?></li>
        <li><?= t('The first row must be the header') ?></li>
        <li><?= t('Duplicates are not verified for you') ?></li>
        <li><?= t('The due date must use the ISO format: YYYY-MM-DD') ?></li>
    </ul>
    <p class="margin-top">
        <?= $this->url->icon('download', t('Download CSV template'), 'TaskImportController', 'template', array('project_id' => $project['id'])) ?>
    </p>
</div>
