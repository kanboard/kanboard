<section id="main">
    <div class="page-header">
        <h2><?= Helper\escape($task['project_name']) ?> &gt; <?= t('Task #%d', $task['id']) ?></h2>
        <ul>
            <li><a href="?controller=board&amp;action=show&amp;project_id=<?= $task['project_id'] ?>"><?= t('Back to the board') ?></a></li>
        </ul>
    </div>
    <section class="task-show" id="task-section">

        <?= Helper\template('task_sidebar', array('task' => $task)) ?>

        <div class="task-show-main">
            <?= $task_content_for_layout ?>
        </div>
    </section>
</section>