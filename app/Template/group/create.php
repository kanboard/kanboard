<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-users fa-fw"></i><?= $this->url->link(t('View all groups'), 'group', 'index') ?></li>
        </ul>
    </div>
    <form method="post" action="<?= $this->url->href('group', 'save') ?>" autocomplete="off">
        <?= $this->form->csrf() ?>

        <?= $this->form->label(t('Name'), 'name') ?>
        <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="100"')) ?><br/>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'group', 'index') ?>
        </div>
    </form>
</section>
