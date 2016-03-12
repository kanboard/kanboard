<section id="main">
    <div class="page-header">
        <ul>
            <?php if ($this->user->hasAccess('projectuser', 'managers')): ?>
                <li><i class="fa fa-user fa-fw"></i><?= $this->url->link(t('Users overview'), 'projectuser', 'managers') ?></li>
            <?php endif ?>
            <?php if ($this->user->hasAccess('gantt', 'projects')): ?>
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
                <th class="column-15"><?= $paginator->order(t('Owner'), 'owner_id') ?></th>
                <?php if ($this->user->hasAccess('projectuser', 'managers')): ?>
                    <th class="column-10"><?= t('Users') ?></th>
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
                        <span class="tooltip" title='<?= $this->text->e($this->text->markdown($project['description'])) ?>'>
                            <i class="fa fa-info-circle"></i>
                        </span>
                    <?php endif ?>

                    <?= $this->url->link($this->text->e($project['name']), 'project', 'show', array('project_id' => $project['id'])) ?>
                </td>
                <td>
                    <?= $this->dt->date($project['start_date']) ?>
                </td>
                <td>
                    <?= $this->dt->date($project['end_date']) ?>
                </td>
                <td>
                    <?php if ($project['owner_id'] > 0): ?>
                        <?= $this->text->e($project['owner_name'] ?: $project['owner_username']) ?>
                    <?php endif ?>
                </td>
                <?php if ($this->user->hasAccess('projectuser', 'managers')): ?>
                    <td>
                        <i class="fa fa-users fa-fw"></i>
                        <a href="#" class="tooltip" title="<?= t('Members') ?>" data-href="<?= $this->url->href('Projectuser', 'users', array('project_id' => $project['id'])) ?>"><?= t('Members') ?></a>
                    </td>
                <?php endif ?>
                <td class="dashboard-project-stats">
                    <?php foreach ($project['columns'] as $column): ?>
                        <strong title="<?= t('Task count') ?>"><?= $column['nb_tasks'] ?></strong>
                        <span><?= $this->text->e($column['title']) ?></span>
                    <?php endforeach ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>

        <?= $paginator ?>
    <?php endif ?>
</section>
