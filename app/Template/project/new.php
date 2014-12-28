<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-folder fa-fw"></i><?= $this->a(t('All projects'), 'project', 'index') ?></li>
        </ul>
    </div>
    <section>
    <form method="post" action="<?= $this->u('project', 'save') ?>" autocomplete="off">

        <?= $this->formCsrf() ?>
        <?= $this->formHidden('is_private', $values) ?>
        <?= $this->formLabel(t('Name'), 'name') ?>
        <?= $this->formText('name', $values, $errors, array('autofocus', 'required')) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?> <?= $this->a(t('cancel'), 'project', 'index') ?>
        </div>
    </form>
    </section>
</section>