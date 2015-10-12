<section id="main">
    <div class="page-header">
        <?php if ($this->user->isAdmin()): ?>
        <ul>
            <li><i class="fa fa-user fa-fw"></i><?= $this->url->link(t('All users'), 'user', 'index') ?></li>
            <li><i class="fa fa-plus fa-fw"></i><?= $this->url->link(t('New local user'), 'user', 'create') ?></li>
            <li><i class="fa fa-plus fa-fw"></i><?= $this->url->link(t('New remote user'), 'user', 'create', array('remote' => 1)) ?></li>
        </ul>
        <?php endif ?>
    </div>
    <div class="page-header">
        <h2><?= t('Import') ?></h2>
    </div>
    <form action="<?= $this->url->href('userImport', 'step2') ?>" method="post" enctype="multipart/form-data">
        <?= $this->form->csrf() ?>

        <?= $this->form->label(t('Delimiter'), 'delimiter') ?>
        <?= $this->form->select('delimiter', $delimiters, $values) ?>

        <?= $this->form->label(t('Enclosure'), 'enclosure') ?>
        <?= $this->form->select('enclosure', $enclosures, $values) ?>

        <?= $this->form->label(t('CSV File'), 'file') ?>
        <?= $this->form->file('file', $errors) ?>

        <p class="form-help"><?= t('Maximum size: ') ?><?= is_integer($max_size) ? $this->text->bytes($max_size) : $max_size ?></p>

        <div class="form-actions">
            <input type="submit" value="<?= t('Import') ?>" class="btn btn-blue">
        </div>
    </form>
    <div class="page-header">
        <h2><?= t('Instructions') ?></h2>
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
    <p><i class="fa fa-download fa-fw"></i><?= $this->url->link(t('Download CSV template'), 'userImport', 'template') ?></p>
</section>