<section id="main">
    <div class="page-header">
        <h2><?= t('Remove a category') ?></h2>
    </div>

    <div class="confirm">
        <p class="alert alert-info">
            <?= t('Do you really want to remove this category: "%s"?', $category['name']) ?>
        </p>

        <div class="form-actions">
            <a href="?controller=category&amp;action=remove&amp;project_id=<?= $project['id'] ?>&amp;category_id=<?= $category['id'].Helper\param_csrf() ?>" class="btn btn-red"><?= t('Yes') ?></a>
            <?= t('or') ?> <a href="?controller=category&amp;project_id=<?= $project['id'] ?>"><?= t('cancel') ?></a>
        </div>
    </div>
</section>