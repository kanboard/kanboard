<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <i class="fa fa-table fa-fw"></i>
                <?= $this->url->link(t('Back to the board'), 'board', 'show', array('project_id' => $task['project_id']), false, '', '', false, 'swimlane-'.$task['swimlane_id']) ?>
            </li>
            <?php if ($this->user->isManager($task['project_id'])): ?>
            <li>
                <i class="fa fa-cog fa-fw"></i>
                <?= $this->url->link(t('Project settings'), 'project', 'show', array('project_id' => $task['project_id'])) ?>
            </li>
            <?php endif ?>
            <li>
                <i class="fa fa-calendar fa-fw"></i>
                <?= $this->url->link(t('Project calendar'), 'calendar', 'show', array('project_id' => $task['project_id'])) ?>
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