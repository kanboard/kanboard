<?= Helper\js('assets/js/moment.min.js') ?>
<?= Helper\js('assets/js/fullcalendar.min.js') ?>
<?= Helper\css('assets/css/fullcalendar.min.css') ?>

<?= Helper\js('assets/js/calendar.js') ?>

<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-table fa-fw"></i><?= Helper\a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
        </ul>
    </div>
    <section class="sidebar-container">

        <?= Helper\template('calendar/sidebar', array('project' => $project, 'users' => $users, 'categories' => $categories, 'projects' => $projects, 'columns' => $columns, 'status' => $status)) ?>

        <div class="sidebar-content">
            <?= $analytic_content_for_layout ?>
        </div>
    </section>
</section>