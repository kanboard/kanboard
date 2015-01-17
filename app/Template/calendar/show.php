<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-table fa-fw"></i><?= $this->a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
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
                 data-save-url="<?= $this->u('calendar', 'save', array('project_id' => $project['id'])) ?>"
                 data-check-url="<?= $this->u('calendar', 'events', array('project_id' => $project['id'])) ?>"
                 data-check-interval="<?= $check_interval ?>"
                 data-translations='<?= $this->getCalendarTranslations() ?>'
            >
            </div>
        </div>
    </section>
</section>
