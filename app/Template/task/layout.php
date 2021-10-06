<section id="main">
    <?= $this->projectHeader->render($project, 'TaskListController', 'show') ?>
    <?= $this->hook->render('template:task:layout:top', array('task' => $task)) ?>
    <section
        class="sidebar-container" id="task-view"
        data-edit-url="<?= $this->url->href('TaskModificationController', 'edit', array('task_id' => $task['id'])) ?>"
        data-subtask-url="<?= $this->url->href('SubtaskController', 'create', array('task_id' => $task['id'])) ?>"
        data-internal-link-url="<?= $this->url->href('TaskInternalLinkController', 'create', array('task_id' => $task['id'])) ?>"
        data-comment-url="<?= $this->url->href('CommentController', 'create', array('task_id' => $task['id'])) ?>">

        <?= $this->render($sidebar_template, array('task' => $task)) ?>

        <div class="sidebar-content">
            <?= $content_for_sublayout ?>
        </div>
    </section>
</section>
