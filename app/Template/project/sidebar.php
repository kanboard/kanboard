<div class="sidebar">
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li>
            <?= $this->url->link(t('Summary'), 'project', 'show', array('project_id' => $project['id'])) ?>
        </li>

        <?php if ($this->user->isManager($project['id'])): ?>
            <li>
                <?= $this->url->link(t('Public access'), 'project', 'share', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <?= $this->url->link(t('Integrations'), 'project', 'integration', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <?= $this->url->link(t('Edit project'), 'project', 'edit', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <?= $this->url->link(t('Columns'), 'column', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <?= $this->url->link(t('Swimlanes'), 'swimlane', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <?= $this->url->link(t('Categories'), 'category', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <?php if ($this->user->isAdmin() || $project['is_private'] == 0): ?>
            <li>
                <?= $this->url->link(t('Users'), 'project', 'users', array('project_id' => $project['id'])) ?>
            </li>
            <?php endif ?>
            <li>
                <?= $this->url->link(t('Automatic actions'), 'action', 'index', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <?= $this->url->link(t('Duplicate'), 'project', 'duplicate', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <?php if ($project['is_active']): ?>
                    <?= $this->url->link(t('Disable'), 'project', 'disable', array('project_id' => $project['id']), true) ?>
                <?php else: ?>
                    <?= $this->url->link(t('Enable'), 'project', 'enable', array('project_id' => $project['id']), true) ?>
                <?php endif ?>
            </li>
            <?php if ($this->user->isAdmin()): ?>
                <li>
                    <?= $this->url->link(t('Remove'), 'project', 'remove', array('project_id' => $project['id'])) ?>
                </li>
            <?php endif ?>
        <?php endif ?>
    </ul>
    <div class="sidebar-collapse"><a href="#" title="<?= t('Hide sidebar') ?>"><i class="fa fa-chevron-left"></i></a></div>
    <div class="sidebar-expand" style="display: none"><a href="#" title="<?= t('Expand sidebar') ?>"><i class="fa fa-chevron-right"></i></a></div>
</div>
