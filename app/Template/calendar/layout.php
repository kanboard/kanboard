<?= Helper\js('assets/js/d3.v3.4.8.min.js') ?>
<?= Helper\js('assets/js/dimple.v2.1.0.min.js') ?>
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

        <?= Helper\template('calendar/sidebar', array('project' => $project, 'users' => $users, 'categories' => $categories, 'ical_url' => $ical_url, 'projects' => $projects, 'columns' => $columns, 'status' => $status)) ?>

        <div class="sidebar-content">
            <?= $analytic_content_for_layout ?>
        </div>
    </section>
</section>