<section id="main">
    <section>
        <h3><?= t('Change category for the task "%s"', $values['title']) ?></h3>
        <form method="post" action="<?= $this->u('board', 'updateCategory', array('task_id' => $values['id'], 'project_id' => $values['project_id'])) ?>">

            <?= $this->formCsrf() ?>

            <?= $this->formHidden('id', $values) ?>
            <?= $this->formHidden('project_id', $values) ?>

            <?= $this->formLabel(t('Category'), 'category_id') ?>
            <?= $this->formSelect('category_id', $categories_list, $values) ?><br/>

            <div class="form-actions">
                <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
                <?= t('or') ?>
                <?= $this->a(t('cancel'), 'board', 'show', array('project_id' => $project['id'])) ?>
            </div>
        </form>
    </section>

</section>