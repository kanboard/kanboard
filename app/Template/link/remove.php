<section id="main">
    <div class="page-header">
        <h2><?= t('Remove a link') ?></h2>
    </div>

    <div class="confirm">
        <p class="alert alert-info">
            <?= t('Do you really want to remove this link: "%s"?', $link['name']) ?>
        </p>

        <div class="form-actions">
            <?= Helper\a(t('Yes'), 'link', 'remove', array('project_id' => $project['id'], 'link_id' => $link['id']), true, 'btn btn-red') ?>
            <?= t('or') ?>
            <?= Helper\a(t('cancel'), 'link', 'index', array('project_id' => $project['id'])) ?>
        </div>
    </div>
</section>