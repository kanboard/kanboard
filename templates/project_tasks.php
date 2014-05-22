<section id="main">
    <div class="page-header">
        <h2><?= t('Completed tasks for "%s"', $project['name']) ?><span id="page-counter"> (<?= $nb_tasks ?>)</span></h2>
        <ul>
            <li><a href="?controller=board&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= t('Back to the board') ?></a></li>
            <li><a href="?controller=project&amp;action=search&amp;project_id=<?= $project['id'] ?>"><?= t('Search') ?></a></li>
            <li><a href="?controller=project&amp;action=index"><?= t('List of projects') ?></a></li>
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
                <th><?= t('Category') ?></th>
                <th><?= t('Title') ?></th>
                <th><?= t('Assignee') ?></th>
                <th><?= t('Due date') ?></th>
                <th><?= t('Date created') ?></th>
                <th><?= t('Date completed') ?></th>
            </tr>
            <?php foreach ($tasks as $task): ?>
            <tr>
                <td class="task task-<?= $task['color_id'] ?>">
                    <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>" title="<?= t('View this task') ?>"><?= Helper\escape($task['id']) ?></a>
                </td>
                <td>
                    <?= Helper\in_list($task['column_id'], $columns) ?>
                </td>
                <td>
                    <?= Helper\in_list($task['category_id'], $categories, '') ?>
                </td>
                <td>
                    <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>" title="<?= t('View this task') ?>"><?= Helper\escape($task['title']) ?></a>
                    <div class="task-table-icons">
                        <?php if (! empty($task['nb_comments'])): ?>
                            <?= $task['nb_comments'] ?> <i class="fa fa-comment-o" title="<?= p($task['nb_comments'], t('%d comment', $task['nb_comments']), t('%d comments', $task['nb_comments'])) ?>"></i>
                        <?php endif ?>

                        <?php if (! empty($task['description'])): ?>
                            <i class="fa fa-file-text-o" title="<?= t('Description') ?>"></i>
                        <?php endif ?>
                    </div>
                </td>
                <td>
                    <?php if ($task['username']): ?>
                        <?= Helper\escape($task['username']) ?>
                    <?php else: ?>
                        <?= t('Unassigned') ?>
                    <?php endif ?>
                </td>
                <td>
                    <?= dt('%B %e, %G', $task['date_due']) ?>
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