<section id="main">
    <div class="page-header">
        <h2><?= t('Projects') ?><span id="page-counter"> (<?= $nb_projects ?>)</span></h2>
        <ul>
            <li><a href="?controller=project&amp;action=create"><?= t('New project') ?></a></li>
        </ul>
    </div>
    <section>
    <?php if (empty($projects)): ?>
        <p class="alert"><?= t('No project') ?></p>
    <?php else: ?>
        <table>
            <tr>
                <th><?= t('Project') ?></th>
                <th><?= t('Status') ?></th>
                <th><?= t('Tasks') ?></th>
                <th><?= t('Board') ?></th>

                <?php if ($_SESSION['user']['is_admin'] == 1): ?>
                    <th><?= t('Actions') ?></th>
                <?php endif ?>
            </tr>
            <?php foreach ($projects as $project): ?>
            <tr>
                <td>
                    <a href="?controller=board&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= Helper\escape($project['name']) ?></a>
                </td>
                <td>
                    <?= $project['is_active'] ? t('Active') : t('Inactive') ?>
                </td>
                <td>
                    <?= t('%d tasks on the board', $project['nb_active_tasks']) ?>, <?= t('%d tasks in total', $project['nb_tasks']) ?>
                </td>
                <td>
                    <ul>
                    <?php foreach ($project['columns'] as $column): ?>
                        <li>
                            <?= Helper\escape($column['title']) ?> (<?= $column['nb_active_tasks'] ?>)
                        </li>
                    <?php endforeach ?>
                    </ul>
                </td>
                <?php if ($_SESSION['user']['is_admin'] == 1): ?>
                <td>
                    <ul>
                        <li>
                            <a href="?controller=project&amp;action=edit&amp;project_id=<?= $project['id'] ?>"><?= t('Edit project') ?></a>
                        </li>
                        <li>
                            <a href="?controller=board&amp;action=edit&amp;project_id=<?= $project['id'] ?>"><?= t('Edit board') ?></a>
                        </li>
                        <li>
                            <?php if ($project['is_active']): ?>
                                <a href="?controller=project&amp;action=disable&amp;project_id=<?= $project['id'] ?>"><?= t('Disable') ?></a>
                            <?php else: ?>
                                <a href="?controller=project&amp;action=enable&amp;project_id=<?= $project['id'] ?>"><?= t('Enable') ?></a>
                            <?php endif ?>
                        </li>
                        <li>
                            <a href="?controller=project&amp;action=confirm&amp;project_id=<?= $project['id'] ?>"><?= t('Remove') ?></a>
                        </li>
                    </ul>
                </td>
                <?php endif ?>
            </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>
    </section>
</section>