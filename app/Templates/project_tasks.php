<section id="main">
    <div class="page-header">
        <h2><?= t('Completed tasks for "%s"', $project['name']) ?><span id="page-counter"> (<?= $nb_tasks ?>)</span></h2>
        <ul>
            <li><a href="?controller=board&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= t('Back to the board') ?></a></li>
            <li><a href="?controller=project&amp;action=search&amp;project_id=<?= $project['id'] ?>"><?= t('Search') ?></a></li>
            <li><a href="?controller=project&amp;action=activity&amp;project_id=<?= $project['id'] ?>"><?= t('Activity') ?></a></li>
            <li><a href="?controller=project&amp;action=index"><?= t('List of projects') ?></a></li>
        </ul>
    </div>
    <section>
    <?php if (empty($tasks)): ?>
        <p class="alert"><?= t('No task') ?></p>
    <?php else: ?>
        <?= Helper\template('task_table', array('tasks' => $tasks, 'categories' => $categories, 'columns' => $columns)) ?>
    <?php endif ?>
    </section>
</section>