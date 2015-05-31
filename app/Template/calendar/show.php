<section id="main">
    <div class="page-header">
        <ul>
            <li>
            <span class="dropdown">
                <span>
                    <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Actions') ?></a>
                    <ul>
                        <?= $this->render('project/dropdown', array('project' => $project)) ?>
                    </ul>
                </span>
            </span>
        </li>
            <li>
                <i class="fa fa-table fa-fw"></i>
                <?= $this->url->link(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <i class="fa fa-folder fa-fw"></i>
                <?= $this->url->link(t('All projects'), 'project', 'index') ?>
            </li>
        </ul>
    </div>
    <section class="sidebar-container">

        <?= $this->render('calendar/sidebar', array(
            'project' => $project,
            'users_list' => $users_list,
            'categories_list' => $categories_list,
            'columns_list' => $columns_list,
            'swimlanes_list' => $swimlanes_list,
            'colors_list' => $colors_list,
            'status_list' => $status_list
        )) ?>

        <div class="sidebar-content">
            <div id="calendar"
                 data-project-id="<?= $project['id'] ?>"
                 data-save-url="<?= $this->url->href('calendar', 'save') ?>"
                 data-check-url="<?= $this->url->href('calendar', 'project', array('project_id' => $project['id'])) ?>"
                 data-check-interval="<?= $check_interval ?>"
            >
            </div>
        </div>
    </section>
</section>