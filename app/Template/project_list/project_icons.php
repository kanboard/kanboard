<div class="table-list-icons">
    &nbsp;

    <?php if ($project['is_public']): ?>
        <em class="fa fa-share-alt fa-fw" title="<?= t('Shared project') ?>"></em>
    <?php endif ?>

    <?php if ($project['is_private']): ?>
        <em class="fa fa-lock fa-fw" title="<?= t('Personal project') ?>"></em>
    <?php endif ?>

    <?php if ($this->user->hasAccess('ProjectUserOverviewController', 'managers')): ?>
        <?= $this->app->tooltipLink('<em class="fa fa-users"></em>', $this->url->href('ProjectUserOverviewController', 'users', array('project_id' => $project['id']))) ?>
    <?php endif ?>

    <?php if (! empty($project['description'])): ?>
        <?= $this->app->tooltipMarkdown($project['description']) ?>
    <?php endif ?>

    <?php if ($project['is_active'] == 0): ?>
        <em class="fa fa-ban fa-fw" aria-hidden="true" title="<?= t('Closed') ?>"></em><?= t('Closed') ?>
    <?php endif ?>
</div>
