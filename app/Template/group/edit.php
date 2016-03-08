<section id="main">
    <div class="page-header">
        <ul class="btn-group">
            <li><?= $this->url->buttonLink('<fa-users>' . t('View all groups'), 'group', 'index') ?></li>
        </ul>
    </div>
    <form method="post" action="<?= $this->url->href('group', 'update') ?>" autocomplete="off">
        <?= $this->form->csrf() ?>

        <?= $this->form->hidden('id', $values) ?>
        <?= $this->form->hidden('external_id', $values) ?>

        <?= $this->form->label(t('Name'), 'name') ?>
        <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="100"')) ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'group', 'index') ?>
        </div>
    </form>
</section>
