<div class="table-list-icons">
    &nbsp;

    <?php if ($project['is_public']): ?>
        <i class="fa fa-share-alt fa-fw" title="<?= t('Shared project') ?>"></i>
    <?php endif ?>

    <?php if ($project['is_private']): ?>
        <i class="fa fa-lock fa-fw" title="<?= t('Personal project') ?>"></i>
    <?php endif ?>

    <?php if ($this->user->hasAccess('ProjectUserOverviewController', 'managers')): ?>
        <?= $this->app->tooltipLink('<i class="fa fa-users"></i>', $this->url->href('ProjectUserOverviewController', 'users', array('project_id' => $project['id']))) ?>
    <?php endif ?>

    <?php if (! empty($project['description'])): ?>
        <?= $this->app->tooltipMarkdown($project['description']) ?>
    <?php endif ?>

    <?php if ($project['is_active'] == 0): ?>
        <i class="fa fa-ban fa-fw" aria-hidden="true" title="<?= t('Closed') ?>"></i><?= t('Closed') ?>
    <?php endif ?>
</div>
