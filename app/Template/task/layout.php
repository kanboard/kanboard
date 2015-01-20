<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <i class="fa fa-table fa-fw"></i>
                <?= $this->a(t('Back to the board'), 'board', 'show', array('project_id' => $task['project_id'])) ?>
            </li>
            <li>
                <i class="fa fa-calendar fa-fw"></i>
                <?= $this->a(t('Calendar'), 'calendar', 'show', array('project_id' => $task['project_id'])) ?>
            </li>
        </ul>
    </div>
    <section class="sidebar-container" id="task-section">

        <?= $this->render('task/sidebar', array('task' => $task)) ?>

        <div class="sidebar-content">
            <?= $task_content_for_layout ?>
        </div>
    </section>
</section>