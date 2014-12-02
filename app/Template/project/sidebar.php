<div class="sidebar">
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li>
            <?= Helper\a(t('Summary'), 'project', 'show', array('project_id' => $project['id'])) ?>
        </li>

        <?php if (Helper\is_admin() || $project['is_private']): ?>
        <li>
            <?= Helper\a(t('Public access'), 'project', 'share', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Edit project'), 'project', 'edit', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Edit board'), 'board', 'edit', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Category management'), 'category', 'index', array('project_id' => $project['id'])) ?>
        </li>
        <?php if (Helper\is_admin()): ?>
            <li>
                <?= Helper\a(t('User management'), 'project', 'users', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>
        <li>
            <?= Helper\a(t('Automatic actions'), 'action', 'index', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Duplicate'), 'project', 'duplicate', array('project_id' => $project['id']), true) ?>
        </li>
        <li>
            <?php if ($project['is_active']): ?>
                <?= Helper\a(t('Disable'), 'project', 'disable', array('project_id' => $project['id']), true) ?>
            <?php else: ?>
                <?= Helper\a(t('Enable'), 'project', 'enable', array('project_id' => $project['id']), true) ?>
            <?php endif ?>
        </li>
        <li>
            <?= Helper\a(t('Remove'), 'project', 'remove', array('project_id' => $project['id'])) ?>
        </li>
        <?php endif ?>
    </ul>

    <?php if (Helper\is_admin() || $project['is_private']): ?>
    <h2><?= t('Exports') ?></h2>
    <ul>
        <li>
            <?= Helper\a(t('Tasks'), 'project', 'exportTasks', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= Helper\a(t('Daily project summary'), 'project', 'exportDailyProjectSummary', array('project_id' => $project['id'])) ?>
        </li>
    </li>
    <?php endif ?>
</div>