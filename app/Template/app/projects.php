<h2><?= t('My projects') ?></h2>
<?php if (empty($projects)): ?>
    <p class="alert"><?= t('Your are not member of any project.') ?></p>
<?php else: ?>
    <table class="table-fixed">
        <tr>
            <th class="column-8"><?= $this->order('Id', 'id', $pagination) ?></th>
            <th class="column-20"><?= $this->order(t('Project'), 'name', $pagination) ?></th>
            <th><?= t('Columns') ?></th>
        </tr>
        <?php foreach ($projects as $project): ?>
        <tr>
            <td>
                <?= $this->a('#'.$project['id'], 'board', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link') ?>
            </td>
            <td>
                <?php if ($this->isManager($project['id'])): ?>
                    <?= $this->a('<i class="fa fa-cog"></i>', 'project', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Settings')) ?>&nbsp;
                <?php endif ?>
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

    <?= $this->paginate($pagination) ?>
<?php endif ?>