<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('No project') ?></p>
<?php else: ?>
    <table class="table-fixed">
        <tr>
            <th class="column-20"><?= $paginator->order(t('User'), 'users.username') ?></th>
            <th class="column-25"><?= $paginator->order(t('Project'), 'projects.name') ?></th>
            <th><?= t('Columns') ?></th>
        </tr>
        <?php foreach ($paginator->getCollection() as $project): ?>
        <tr>
            <td>
                <?= $this->e($this->user->getFullname($project)) ?>
            </td>
            <td>
                <?= $this->url->link('<i class="fa fa-th"></i>', 'board', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Board')) ?>
                <?= $this->url->link('<i class="fa fa-sliders fa-fw"></i>', 'gantt', 'project', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Gantt chart')) ?>
                <?= $this->url->link('<i class="fa fa-cog fa-fw"></i>', 'project', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Project settings')) ?>

                <?= $this->e($project['project_name']) ?>
            </td>
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