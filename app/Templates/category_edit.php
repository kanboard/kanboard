<section id="main">
    <div class="page-header">
        <h2><?= t('Category modification for the project "%s"', $project['name']) ?></h2>
        <ul>
            <li><a href="?controller=project"><?= t('All projects') ?></a></li>
        </ul>
    </div>
    <section>

    <form method="post" action="?controller=category&amp;action=update&amp;project_id=<?= $project['id'] ?>" autocomplete="off">
        <?= Helper\form_csrf() ?>
        <?= Helper\form_hidden('id', $values) ?>
        <?= Helper\form_hidden('project_id', $values) ?>

        <?= Helper\form_label(t('Category Name'), 'name') ?>
        <?= Helper\form_text('name', $values, $errors, array('required')) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        </div>
    </form>

    </section>
</section>