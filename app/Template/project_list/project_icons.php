<div class="table-list-icons">
    &nbsp;

    <?php if ($project['is_public']): ?>
        <i class="fa fa-share-alt fa-fw" title="<?= t('Shared project') ?>"></i>
    <?php endif ?>

    <?php if ($project['is_private']): ?>
        <i class="fa fa-lock fa-fw" title="<?= t('Private project') ?>"></i>
    <?php endif ?>

    <?php if ($this->user->hasAccess('ProjectUserOverviewController', 'managers')): ?>
        <span class="tooltip" title="<?= t('Members') ?>" data-href="<?= $this->url->href('ProjectUserOverviewController', 'users', array('project_id' => $project['id'])) ?>"><i class="fa fa-users"></i></span>&nbsp;
    <?php endif ?>

    <?php if (! empty($project['description'])): ?>
        <span class="tooltip" title="<?= $this->text->markdownAttribute($project['description']) ?>">
            <i class="fa fa-info-circle"></i>
        </span>
    <?php endif ?>

    <?php if ($project['is_active'] == 0): ?>
        <i class="fa fa-ban fa-fw" aria-hidden="true" title="<?= t('Closed') ?>"></i><?= t('Closed') ?>
    <?php endif ?>
</div>
