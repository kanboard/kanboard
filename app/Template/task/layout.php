<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <i class="fa fa-th fa-fw"></i>
                <?= $this->url->link(t('Back to the board'), 'board', 'show', array('project_id' => $task['project_id']), false, '', '', false, $task['swimlane_id'] != 0 ? 'swimlane-'.$task['swimlane_id'] : '') ?>
            </li>
            <li>
                <i class="fa fa-calendar fa-fw"></i>
                <?= $this->url->link(t('Back to the calendar'), 'calendar', 'show', array('project_id' => $task['project_id'])) ?>
            </li>
            <?php if ($this->user->isProjectManagementAllowed($task['project_id'])): ?>
            <li>
                <i class="fa fa-cog fa-fw"></i>
                <?= $this->url->link(t('Project settings'), 'project', 'show', array('project_id' => $task['project_id'])) ?>
            </li>
            <?php endif ?>
        </ul>
    </div>
    <section class="sidebar-container" id="task-section">

        <?= $this->render('task/sidebar', array('task' => $task)) ?>

        <div class="sidebar-content">
            <?= $task_content_for_layout ?>
        </div>
    </section>
</section>