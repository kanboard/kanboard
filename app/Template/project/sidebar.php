<div class="sidebar">
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li>
            <?= $this->a(t('Summary'), 'project', 'show', array('project_id' => $project['id'])) ?>
        </li>

        <?php if ($this->acl->isAdminUser() || $project['is_private']): ?>
        <li>
            <?= $this->a(t('Public access'), 'project', 'share', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->a(t('Edit project'), 'project', 'edit', array('project_id' => $project['id'])) ?>
        </li>
        <?php endif ?>
        <?php if ($this->acl->isAdminUser() || $is_owner || $project['is_private']): ?>
            <li>
                <?= $this->a(t('Edit board'), 'board', 'edit', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <?= $this->a(t('Category management'), 'category', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <?= $this->a(t('Swimlanes'), 'swimlane', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <?php if ($project['is_private'] == 0): ?>
            <li>
                <?= $this->a(t('User management'), 'project', 'users', array('project_id' => $project['id'])) ?>
            </li>
            <?php endif ?>
            <li>
                <?= $this->a(t('Automatic actions'), 'action', 'index', array('project_id' => $project['id'])) ?>
            </li>
        <?php endif ?>
        <?php if ($this->acl->isAdminUser() || $project['is_private']): ?>
        <li>
            <?= $this->a(t('Duplicate'), 'project', 'duplicate', array('project_id' => $project['id']), true) ?>
        </li>
        <li>
            <?php if ($project['is_active']): ?>
                <?= $this->a(t('Disable'), 'project', 'disable', array('project_id' => $project['id']), true) ?>
            <?php else: ?>
                <?= $this->a(t('Enable'), 'project', 'enable', array('project_id' => $project['id']), true) ?>
            <?php endif ?>
        </li>
        <li>
            <?= $this->a(t('Remove'), 'project', 'remove', array('project_id' => $project['id'])) ?>
        </li>
        <?php endif ?>
    </ul>

    <?php if ($this->acl->isAdminUser() || $project['is_private']): ?>
    <h2><?= t('Exports') ?></h2>
    <ul>
        <li>
            <?= $this->a(t('Tasks'), 'project', 'exportTasks', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->a(t('Daily project summary'), 'project', 'exportDailyProjectSummary', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
    <?php endif ?>
</div>
