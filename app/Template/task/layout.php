<section id="main">
    <?= $this->projectHeader->render($project, 'Listing', 'show') ?>
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