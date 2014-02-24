<section id="main">
    <div class="page-header">
        <h2><?= t('Completed tasks for "%s"', $project['name']) ?><span id="page-counter"> (<?= $nb_tasks ?>)</span></h2>
        <ul>
            <li><a href="?controller=board&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= t('Back to the board') ?></a></li>
            <li><a href="?controller=project&amp;action=index"><?= t('List of projects') ?></a></li>
            <?php if (Helper\is_admin()): ?>
                <li><a href="?controller=project&amp;action=create"><?= t('New project') ?></a></li>
            <?php endif ?>
        </ul>
    </div>
    <section>
    <?php if (empty($tasks)): ?>
        <p class="alert"><?= t('No task') ?></p>
    <?php else: ?>
        <table>
            <tr>
                <th><?= t('Id') ?></th>
                <th><?= t('Column') ?></th>
                <th><?= t('Title') ?></th>
                <th><?= t('Assignee') ?></th>
                <th><?= t('Date created') ?></th>
                <th><?= t('Date completed') ?></th>
            </tr>
            <?php foreach ($tasks as $task): ?>
            <tr>
                <td class="task task-<?= $task['color_id'] ?>">
                    <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>" title="<?= t('Show this task') ?>"><?= Helper\escape($task['id']) ?></a>
                </td>
                <td>
                    <?= Helper\escape($task['column_title']) ?>
                </td>
                <td>
                    <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>" title="<?= t('Show this task') ?>"><?= Helper\escape($task['title']) ?></a>
                </td>
                <td>
                    <?= Helper\escape($task['username']) ?>
                </td>
                <td>
                    <?= dt('%B %e, %G at %k:%M %p', $task['date_creation']) ?>
                </td>
                <td>
                    <?php if ($task['date_completed']): ?>
                        <?= dt('%B %e, %G at %k:%M %p', $task['date_completed']) ?>
                    <?php endif ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>
    </section>
</section>