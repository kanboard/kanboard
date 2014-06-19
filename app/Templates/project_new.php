<section id="main">
    <div class="page-header">
        <h2><?= t('New project') ?></h2>
        <ul>
            <li><a href="?controller=project"><?= t('All projects') ?></a></li>
        </ul>
    </div>
    <section>
    <form method="post" action="?controller=project&amp;action=save" autocomplete="off">

        <?= Helper\form_csrf() ?>
        <?= Helper\form_label(t('Name'), 'name') ?>
        <?= Helper\form_text('name', $values, $errors, array('autofocus', 'required')) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?> <a href="?controller=project"><?= t('cancel') ?></a>
        </div>
    </form>
    </section>
</section>