<section id="main">
    <div class="page-header">
        <ul>
            <?php if ($this->user->isProjectAdmin() || $this->user->isAdmin()): ?>
                <li><i class="fa fa-plus fa-fw"></i><?= $this->url->link(t('New project'), 'project', 'create') ?></li>
            <?php endif ?>
            <li><i class="fa fa-lock fa-fw"></i><?= $this->url->link(t('New private project'), 'project', 'create', array('private' => 1)) ?></li>
            <?php if ($this->user->isProjectAdmin() || $this->user->isAdmin()): ?>
                <li><i class="fa fa-user fa-fw"></i><?= $this->url->link(t('Users overview'), 'projectuser', 'managers') ?></li>
                <li><i class="fa fa-sliders fa-fw"></i><?= $this->url->link(t('Projects Gantt chart'), 'gantt', 'projects') ?></li>
            <?php endif ?>
        </ul>
    </div>
    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('No project') ?></p>
    <?php else: ?>
        <table class="table-stripped table-small">
            <tr>
                <th class="column-3"><?= $paginator->order(t('Id'), 'id') ?></th>
                <th class="column-5"><?= $paginator->order(t('Status'), 'is_active') ?></th>
                <th class="column-15"><?= $paginator->order(t('Project'), 'name') ?></th>
                <th class="column-8"><?= $paginator->order(t('Start date'), 'start_date') ?></th>
                <th class="column-8"><?= $paginator->order(t('End date'), 'end_date') ?></th>
                <?php if ($this->user->isAdmin() || $this->user->isProjectAdmin()): ?>
                    <th class="column-12"><?= t('Managers') ?></th>
                    <th class="column-12"><?= t('Members') ?></th>
                <?php endif ?>
                <th><?= t('Columns') ?></th>
            </tr>
            <?php foreach ($paginator->getCollection() as $project): ?>
            <tr>
                <td>
                    <?= $this->url->link('#'.$project['id'], 'board', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link') ?>
                </td>
                <td>
                    <?php if ($project['is_active']): ?>
                        <?= t('Active') ?>
                    <?php else: ?>
                        <?= t('Inactive') ?>
                    <?php endif ?>
                </td>
                <td>
                    <?= $this->url->link('<i class="fa fa-th"></i>', 'board', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Board')) ?>
                    <?= $this->url->link('<i class="fa fa-sliders fa-fw"></i>', 'gantt', 'project', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Gantt chart')) ?>

                    <?php if ($project['is_public']): ?>
                        <i class="fa fa-share-alt fa-fw" title="<?= t('Shared project') ?>"></i>
                    <?php endif ?>
                    <?php if ($project['is_private']): ?>
                        <i class="fa fa-lock fa-fw" title="<?= t('Private project') ?>"></i>
                    <?php endif ?>

                    <?php if (! empty($project['description'])): ?>
                        <span class="tooltip" title='<?= $this->e($this->text->markdown($project['description'])) ?>'>
                            <i class="fa fa-info-circle"></i>
                        </span>
                    <?php endif ?>

                    <?= $this->url->link($this->e($project['name']), 'project', 'show', array('project_id' => $project['id'])) ?>
                </td>
                <td>
                    <?= $project['start_date'] ?>
                </td>
                <td>
                    <?= $project['end_date'] ?>
                </td>
                <?php if ($this->user->isAdmin() || $this->user->isProjectAdmin()): ?>
                <td>
                    <ul class="no-bullet">
                    <?php foreach ($project['managers'] as $user_id => $user_name): ?>
                        <li><?= $this->url->link($this->e($user_name), 'projectuser', 'opens', array('user_id' => $user_id)) ?></li>
                    <?php endforeach ?>
                    </ul>
                </td>
                <td>
                    <?php if ($project['is_everybody_allowed'] == 1): ?>
                        <?= t('Everybody') ?>
                    <?php else: ?>
                        <ul class="no-bullet">
                        <?php foreach ($project['members'] as $user_id => $user_name): ?>
                            <li><?= $this->url->link($this->e($user_name), 'projectuser', 'opens', array('user_id' => $user_id)) ?></li>
                        <?php endforeach ?>
                        </ul>
                    <?php endif ?>
                </td>
                <?php endif ?>
                <td class="dashboard-project-stats">
                    <?php foreach ($project['columns'] as $column): ?>
                        <strong title="<?= t('Task count') ?>"><?= $column['nb_tasks'] ?></strong>
                        <span><?= $this->e($column['title']) ?></span>
                    <?php endforeach ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>

        <?= $paginator ?>
    <?php endif ?>
</section>
