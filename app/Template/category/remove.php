<div class="page-header">
    <h2><?= t('Remove a category') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this category: "%s"?', $category['name']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'CategoryController',
        'remove',
        array('project_id' => $project['id'], 'category_id' => $category['id'])
    ) ?>
</div>
