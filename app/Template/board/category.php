<section id="main">
    <section>
        <h3><?= t('Change category for the task "%s"', $values['title']) ?></h3>
        <form method="post" action="<?= Helper\u('board', 'updateCategory', array('task_id' => $values['id'])) ?>">

            <?= Helper\form_csrf() ?>

            <?= Helper\form_hidden('id', $values) ?>
            <?= Helper\form_hidden('project_id', $values) ?>

            <?= Helper\form_label(t('Category'), 'category_id') ?>
            <?= Helper\form_select('category_id', $categories_list, $values) ?><br/>

            <div class="form-actions">
                <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
                <?= t('or') ?>
                <?= Helper\a(t('cancel'), 'board', 'show', array('project_id' => $project['id'])) ?>
            </div>
        </form>
    </section>

</section>