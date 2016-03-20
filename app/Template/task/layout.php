<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <?= $this->render('task/menu', array('task' => $task)) ?>
            </li>
            <li>
                <i class="fa fa-th fa-fw"></i>
                <?= $this->url->link(t('Back to the board'), 'board', 'show', array('project_id' => $task['project_id']), false, '', '', false, $task['swimlane_id'] != 0 ? 'swimlane-'.$task['swimlane_id'] : '') ?>
            </li>
            <li>
                <i class="fa fa-calendar fa-fw"></i>
                <?= $this->url->link(t('Back to the calendar'), 'calendar', 'show', array('project_id' => $task['project_id'])) ?>
            </li>
            <?php if ($this->user->hasProjectAccess('ProjectEdit', 'edit', $task['project_id'])): ?>
            <li>
                <i class="fa fa-cog fa-fw"></i>
                <?= $this->url->link(t('Project settings'), 'project', 'show', array('project_id' => $task['project_id'])) ?>
            </li>
            <?php endif ?>
        </ul>
    </div>
    <section
        class="sidebar-container" id="task-view"
        data-edit-url="<?= $this->url->href('taskmodification', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"
        data-description-url="<?= $this->url->href('taskmodification', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"
        data-subtask-url="<?= $this->url->href('subtask', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"
        data-internal-link-url="<?= $this->url->href('TaskInternalLink', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"
        data-comment-url="<?= $this->url->href('comment', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>">

        <?= $this->render($sidebar_template, array('task' => $task)) ?>

        <div class="sidebar-content">
            <?= $content_for_sublayout ?>
        </div>
    </section>
</section>