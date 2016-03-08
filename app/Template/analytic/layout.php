<section id="main">
    <div class="page-header">
        <ul>
            <li>
            <span class="dropdown">
                <span>
                    <a href="#" class="dropdown-menu btn"><?= t('Actions') ?> <i class="fa fa-caret-down"></i></a>
                    <ul>
                        <?= $this->render('project/dropdown', array('project' => $project)) ?>
                    </ul>
                </span>
            </span>
            </li>
        </ul>
        <ul class="btn-group">
            <li>
                <?= $this->url->buttonLink('<fa-th>' . t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <?= $this->url->buttonLink('<fa-calendar>' . t('Back to the calendar'), 'calendar', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <?php if ($this->user->hasProjectAccess('ProjectEdit', 'edit', $project['id'])): ?>
            <li>
                <?= $this->url->buttonLink('<fa-cog>' . t('Project settings'), 'project', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <?php endif ?>
            <li>
                <?= $this->url->buttonLink('<fa-folder>' . t('All projects'), 'project', 'index') ?>
            </li>
        </ul>
    </div>
    <section class="sidebar-container">
        <?= $this->render($sidebar_template, array('project' => $project)) ?>

        <div class="sidebar-content">
            <?= $content_for_sublayout ?>
        </div>
    </section>
</section>

<?= $this->asset->js('assets/js/vendor/d3.v3.min.js') ?>
<?= $this->asset->js('assets/js/vendor/c3.min.js') ?>
