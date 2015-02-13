<section id="main">
    <div class="page-header">
        <h2><?= t('Remove a link') ?></h2>
    </div>

    <div class="confirm">
        <p class="alert alert-info">
            <?= t('Do you really want to remove this link: "%s"?', t($link[0]['label']).(isset($link[1]['label']) ? ' | '.t($link[1]['label']) : '')) ?>
        </p>

        <div class="form-actions">
            <?= $this->a(t('Yes'), 'link', 'remove', array('project_id' => $project['id'], 'link_id' => $link[0]['link_id']), true, 'btn btn-red') ?>
            <?= t('or') ?>
            <?= $this->a(t('cancel'), 'link', 'index', array('project_id' => $project['id'])) ?>
        </div>
    </div>
</section>