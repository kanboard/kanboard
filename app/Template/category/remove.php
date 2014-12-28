<section id="main">
    <div class="page-header">
        <h2><?= t('Remove a category') ?></h2>
    </div>

    <div class="confirm">
        <p class="alert alert-info">
            <?= t('Do you really want to remove this category: "%s"?', $category['name']) ?>
        </p>

        <div class="form-actions">
            <?= $this->a(t('Yes'), 'category', 'remove', array('project_id' => $project['id'], 'category_id' => $category['id']), true, 'btn btn-red') ?>
            <?= t('or') ?>
            <?= $this->a(t('cancel'), 'category', 'index', array('project_id' => $project['id'])) ?>
        </div>
    </div>
</section>