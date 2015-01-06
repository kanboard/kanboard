<section id="main">
    <div class="page-header">
        <ul>
            <?php if ($this->userSession->isAdmin()): ?>
                <li><i class="fa fa-plus fa-fw"></i><?= $this->a(t('New project'), 'project', 'create') ?></li>
            <?php endif ?>
            <li><i class="fa fa-lock fa-fw"></i><?= $this->a(t('New private project'), 'project', 'create', array('private' => 1)) ?></li>
        </ul>
    </div>
    <section>
    <?php if (empty($active_projects) && empty($inactive_projects)): ?>
        <p class="alert"><?= t('No project') ?></p>
    <?php else: ?>

        <?php if (! empty($active_projects)): ?>
            <h3><?= t('Active projects') ?></h3>
            <ul class="project-listing">
                <?php foreach ($active_projects as $project): ?>
                    <li>
                        <?php if ($project['is_public']): ?>
                            <i class="fa fa-share-alt fa-fw"></i>
                        <?php endif ?>
                        <?php if ($project['is_private']): ?>
                            <i class="fa fa-lock fa-fw"></i>
                        <?php endif ?>
                        <?= $this->a($this->e($project['name']), 'project', 'show', array('project_id' => $project['id'])) ?>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>

        <?php if (! empty($inactive_projects)): ?>
            <h3><?= t('Inactive projects') ?></h3>
            <ul class="project-listing">
                <?php foreach ($inactive_projects as $project): ?>
                    <li>
                        <?php if ($project['is_private']): ?>
                            <i class="fa fa-lock"></i>
                        <?php endif ?>
                        <?= $this->a($this->e($project['name']), 'project', 'show', array('project_id' => $project['id'])) ?>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>

    <?php endif ?>
    </section>
</section>