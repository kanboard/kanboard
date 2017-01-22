<section id="main">
    <div class="page-header">
        <ul>
            <?php if ($this->user->hasAccess('ProjectCreationController', 'create')): ?>
                <li>
                    <?= $this->modal->medium('plus', t('New project'), 'ProjectCreationController', 'create') ?>
                </li>
            <?php endif ?>
            <?php if ($this->app->config('disable_private_project', 0) == 0): ?>
                <li>
                    <?= $this->modal->medium('lock', t('New private project'), 'ProjectCreationController', 'createPrivate') ?>
                </li>
            <?php endif ?>
            <li>
                <?= $this->url->icon('folder', t('Projects list'), 'ProjectListController', 'show') ?>
            </li>
            <?php if ($this->user->hasAccess('ProjectUserOverviewController', 'managers')): ?>
                <li>
                    <?= $this->url->icon('user', t('Users overview'), 'ProjectUserOverviewController', 'managers') ?>
                </li>
            <?php endif ?>
        </ul>
    </div>
    <section>
        <?php if (empty($projects)): ?>
            <p class="alert"><?= t('No project') ?></p>
        <?php else: ?>
            <div
                id="gantt-chart"
                data-records='<?= json_encode($projects, JSON_HEX_APOS) ?>'
                data-save-url="<?= $this->url->href('ProjectGanttController', 'save') ?>"
                data-label-project-manager="<?= t('Project managers') ?>"
                data-label-project-member="<?= t('Project members') ?>"
                data-label-gantt-link="<?= t('Gantt chart for this project') ?>"
                data-label-board-link="<?= t('Project board') ?>"
                data-label-start-date="<?= t('Start date:') ?>"
                data-label-end-date="<?= t('End date:') ?>"
                data-label-not-defined="<?= t('There is no start date or end date for this project.') ?>"
            ></div>
        <?php endif ?>
    </section>
</section>
