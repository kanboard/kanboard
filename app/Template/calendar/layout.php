<?= $this->js('assets/js/moment.min.js') ?>
<?= $this->js('assets/js/fullcalendar.min.js') ?>
<?= $this->css('assets/css/fullcalendar.min.css') ?>

<?= $this->js('assets/js/calendar.js') ?>

<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-table fa-fw"></i><?= $this->a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
        </ul>
    </div>
    <section class="sidebar-container">

        <?= $this->render('calendar/sidebar', array('project' => $project, 'users' => $users, 'categories' => $categories, 'projects' => $projects, 'columns' => $columns, 'status' => $status)) ?>

        <div class="sidebar-content">
            <?= $analytic_content_for_layout ?>
        </div>
    </section>
</section>