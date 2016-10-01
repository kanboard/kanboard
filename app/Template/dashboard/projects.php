<div class="page-header">
    <h2><?= $this->url->link(t('My projects'), 'DashboardController', 'projects', array('user_id' => $user['id'])) ?> (<?= $paginator->getTotal() ?>)</h2>
</div>
<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('Your are not member of any project.') ?></p>
<?php else: ?>
    <table class="table-striped table-small table-scrolling">
        <tr>
            <th class="column-5"><?= $paginator->order('Id', \Kanboard\Model\ProjectModel::TABLE.'.id') ?></th>
            <th class="column-3"><?= $paginator->order('<i class="fa fa-lock fa-fw" title="'.t('Private project').'"></i>', \Kanboard\Model\ProjectModel::TABLE.'.is_private') ?></th>
            <th class="column-25"><?= $paginator->order(t('Project'), \Kanboard\Model\ProjectModel::TABLE.'.name') ?></th>
            <th class="column-10"><?= t('Tasks') ?></th>
            <th><?= t('Columns') ?></th>
        </tr>
        <?php foreach ($paginator->getCollection() as $project): ?>
        <tr>
            <td>
                <?= $this->render('project/dropdown', array('project' => $project)) ?>
            </td>
            <td>
                <?php if ($project['is_private']): ?>
                    <i class="fa fa-lock fa-fw" title="<?= t('Private project') ?>"></i>
                <?php endif ?>
            </td>
            <td>
                <?php if ($this->user->hasProjectAccess('TaskGanttController', 'show', $project['id'])): ?>
                    <?= $this->url->link('<i class="fa fa-sliders fa-fw"></i>', 'TaskGanttController', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Gantt chart')) ?>
                <?php endif ?>

                <?= $this->url->link('<i class="fa fa-list"></i>', 'TaskListController', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link', t('List')) ?>&nbsp;
                <?= $this->url->link('<i class="fa fa-calendar"></i>', 'CalendarController', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Calendar')) ?>&nbsp;

                <?= $this->url->link($this->text->e($project['name']), 'BoardViewController', 'show', array('project_id' => $project['id'])) ?>
                <?php if (! empty($project['description'])): ?>
                    <span class="tooltip" title="<?= $this->text->markdownAttribute($project['description']) ?>">
                        <i class="fa fa-info-circle"></i>
                    </span>
                <?php endif ?>
            </td>
            <td>
                <?= $project['nb_active_tasks'] ?>
            </td>
            <td class="dashboard-project-stats">
                <?php foreach ($project['columns'] as $column): ?>
                    <strong title="<?= t('Task count') ?>"><?= $column['nb_tasks'] ?></strong>
                    <small><?= $this->text->e($column['title']) ?></small>
                <?php endforeach ?>
            </td>

        </tr>
        <?php endforeach ?>
    </table>

    <?= $paginator ?>
<?php endif ?>
