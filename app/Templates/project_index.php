<section id="main">
    <div class="page-header">
        <h2><?= t('Projects') ?><span id="page-counter"> (<?= $nb_projects ?>)</span></h2>
        <?php if (Helper\is_admin()): ?>
        <ul>
            <li><a href="?controller=project&amp;action=create"><?= t('New project') ?></a></li>
        </ul>
        <?php endif ?>
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

                <?php if (Helper\is_admin()): ?>
                    <th><?= t('Actions') ?></th>
                <?php endif ?>
            </tr>
            <?php foreach ($projects as $project): ?>
            <tr>
                <td>
                    <a href="?controller=board&amp;action=show&amp;project_id=<?= $project['id'] ?>" title="project_id=<?= $project['id'] ?>"><?= Helper\escape($project['name']) ?></a>
                </td>
                <td>
                    <?= $project['is_active'] ? t('Active') : t('Inactive') ?>
                </td>
                <td>
                    <ul>
                    <?php if ($project['nb_tasks'] > 0): ?>

                        <?php if ($project['nb_active_tasks'] > 0): ?>
                            <li><a href="?controller=board&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= t('%d tasks on the board', $project['nb_active_tasks']) ?></a></li>
                        <?php endif ?>

                        <?php if ($project['nb_inactive_tasks'] > 0): ?>
                            <li><a href="?controller=project&amp;action=tasks&amp;project_id=<?= $project['id'] ?>"><?= t('%d closed tasks', $project['nb_inactive_tasks']) ?></a></li>
                        <?php endif ?>

                        <li><?= t('%d tasks in total', $project['nb_tasks']) ?></li>

                    <?php else: ?>
                        <li><?= t('no task for this project') ?></li>
                    <?php endif ?>
                    </ul>
                </td>
                <td>
                    <ul>
                    <?php foreach ($project['columns'] as $column): ?>
                        <li>
                            <span title="column_id=<?= $column['id'] ?>"><?= Helper\escape($column['title']) ?></span> (<?= $column['nb_active_tasks'] ?>)
                        </li>
                    <?php endforeach ?>
                    </ul>
                </td>
                <?php if (Helper\is_admin()): ?>
                <td>
                    <ul>
                        <li>
                            <a href="?controller=category&amp;action=index&amp;project_id=<?= $project['id'] ?>"><?= t('Categories') ?></a>
                        </li>
                        <li>
                            <a href="?controller=project&amp;action=edit&amp;project_id=<?= $project['id'] ?>"><?= t('Edit project') ?></a>
                        </li>
                        <li>
                            <a href="?controller=project&amp;action=users&amp;project_id=<?= $project['id'] ?>"><?= t('Edit users access') ?></a>
                        </li>
                        <li>
                            <a href="?controller=board&amp;action=edit&amp;project_id=<?= $project['id'] ?>"><?= t('Edit board') ?></a>
                        </li>
                        <li>
                            <a href="?controller=action&amp;action=index&amp;project_id=<?= $project['id'] ?>"><?= t('Automatic actions') ?></a>
                        </li>
                        <li>
                            <?php if ($project['is_active']): ?>
                                <a href="?controller=project&amp;action=disable&amp;project_id=<?= $project['id'].Helper\param_csrf() ?>"><?= t('Disable') ?></a>
                            <?php else: ?>
                                <a href="?controller=project&amp;action=enable&amp;project_id=<?= $project['id'].Helper\param_csrf() ?>"><?= t('Enable') ?></a>
                            <?php endif ?>
                        </li>
                        <li>
                            <a href="?controller=project&amp;action=confirm&amp;project_id=<?= $project['id'] ?>"><?= t('Remove') ?></a>
                        </li>
                        <li>
                            <a href="?controller=board&amp;action=readonly&amp;token=<?= $project['token'] ?>" target="_blank"><?= t('Public link') ?></a>
                        </li>
                        <li>
                            <a href="?controller=project&amp;action=export&amp;project_id=<?= $project['id'] ?>"><?= t('Tasks Export') ?></a>
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