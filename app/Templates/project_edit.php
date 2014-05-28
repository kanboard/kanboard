<section id="main">
    <div class="page-header">
        <h2><?= t('Edit project') ?></h2>
        <ul>
            <li><a href="?controller=project"><?= t('All projects') ?></a></li>
        </ul>
    </div>
    <section>
    <form method="post" action="?controller=project&amp;action=update&amp;project_id=<?= $values['id'] ?>" autocomplete="off">

        <?= Helper\form_csrf() ?>
        <?= Helper\form_hidden('id', $values) ?>

        <?= Helper\form_label(t('Name'), 'name') ?>
        <?= Helper\form_text('name', $values, $errors, array('required')) ?>

        <?= Helper\form_checkbox('is_active', t('Activated'), 1, isset($values['is_active']) && $values['is_active'] == 1 ? true : false) ?><br/>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?> <a href="?controller=project"><?= t('cancel') ?></a>
        </div>
    </form>
    </section>
</section>