<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-table fa-fw"></i><?= Helper\a(t('Back to the board'), 'board', 'show', array('project_id' => $task['project_id'])) ?></li>
        </ul>
    </div>
    <section class="sidebar-container" id="task-section">

        <?= Helper\template('task/sidebar', array('task' => $task, 'hide_remove_menu' => isset($hide_remove_menu))) ?>

        <div class="sidebar-content">
            <?= $task_content_for_layout ?>
        </div>
    </section>
</section>