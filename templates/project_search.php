<section id="main">
    <div class="page-header">
        <h2>
            <?= t('Search in the project "%s"', $project['name']) ?>
            <?php if (! empty($nb_tasks)): ?>
                <span id="page-counter"> (<?= $nb_tasks ?>)</span>
            <?php endif ?>
        </h2>
        <ul>
            <li><a href="?controller=board&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= t('Back to the board') ?></a></li>
            <li><a href="?controller=project&amp;action=tasks&amp;project_id=<?= $project['id'] ?>"><?= t('Completed tasks') ?></a></li>
            <li><a href="?controller=project&amp;action=index"><?= t('List of projects') ?></a></li>
        </ul>
    </div>
    <section>
    <form method="get" action="?" autocomplete="off">
        <?= Helper\form_hidden('controller', $values) ?>
        <?= Helper\form_hidden('action', $values) ?>
        <?= Helper\form_hidden('project_id', $values) ?>
        <?= Helper\form_text('search', $values, array(), array('autofocus', 'required', 'placeholder="'.t('Search').'"')) ?>
        <input type="submit" value="<?= t('Search') ?>" class="btn btn-blue"/>
    </form>

    <?php if (empty($tasks) && ! empty($values['search'])): ?>
        <p class="alert"><?= t('Nothing found.') ?></p>
    <?php elseif (! empty($tasks)): ?>
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
                <th><?= t('Status') ?></th>
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
                <td>
                    <?php if ($task['is_active'] == \Model\Task::STATUS_OPEN): ?>
                        <?= t('Open') ?>
                    <?php else: ?>
                        <?= t('Closed') ?>
                    <?php endif ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>

    </section>
</section>