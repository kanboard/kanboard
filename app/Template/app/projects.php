<h2><?= t('My projects') ?></h2>
<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('Your are not member of any project.') ?></p>
<?php else: ?>
    <table class="table-fixed">
        <tr>
            <th class="column-8"><?= $paginator->order('Id', 'id') ?></th>
            <th class="column-20"><?= $paginator->order(t('Project'), 'name') ?></th>
            <th><?= t('Columns') ?></th>
        </tr>
        <?php foreach ($paginator->getCollection() as $project): ?>
        <tr>
            <td>
                <?= $this->a('#'.$project['id'], 'board', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link') ?>
            </td>
            <td>
                <?php if ($this->isManager($project['id'])): ?>
                    <?= $this->a('<i class="fa fa-cog"></i>', 'project', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Settings')) ?>&nbsp;
                <?php endif ?>
                
                <?= $this->a('<i class="fa fa-calendar"></i>', 'calendar', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Calendar')) ?>&nbsp;
                <?= $this->a($this->e($project['name']), 'board', 'show', array('project_id' => $project['id'])) ?>
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