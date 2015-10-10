<div class="page-header">
    <h2><?= t('My projects') ?> (<?= $paginator->getTotal() ?>)</h2>
</div>
<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('Your are not member of any project.') ?></p>
<?php else: ?>
    <table class="table-fixed table-small">
        <tr>
            <th class="column-5"><?= $paginator->order('Id', 'id') ?></th>
            <th class="column-3"><?= $paginator->order('<i class="fa fa-lock fa-fw" title="'.t('Private project').'"></i>', 'is_private') ?></th>
            <th class="column-25"><?= $paginator->order(t('Project'), 'name') ?></th>
            <th><?= t('Columns') ?></th>
        </tr>
        <?php foreach ($paginator->getCollection() as $project): ?>
        <tr>
            <td>
                <?= $this->url->link('#'.$project['id'], 'board', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link') ?>
            </td>
            <td>
                <?php if ($project['is_private']): ?>
                    <i class="fa fa-lock fa-fw" title="<?= t('Private project') ?>"></i>
                <?php endif ?>
            </td>
            <td>
                <?php if ($this->user->isProjectManagementAllowed($project['id'])): ?>
                    <?= $this->url->link('<i class="fa fa-sliders fa-fw"></i>', 'gantt', 'project', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Gantt chart')) ?>
                <?php endif ?>

                <?= $this->url->link('<i class="fa fa-list"></i>', 'listing', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link', t('List')) ?>&nbsp;
                <?= $this->url->link('<i class="fa fa-calendar"></i>', 'calendar', 'show', array('project_id' => $project['id']), false, 'dashboard-table-link', t('Calendar')) ?>&nbsp;

                <?= $this->url->link($this->e($project['name']), 'board', 'show', array('project_id' => $project['id'])) ?>
                <?php if (! empty($project['description'])): ?>
                    <span class="tooltip" title='<?= $this->e($this->text->markdown($project['description'])) ?>'>
                        <i class="fa fa-info-circle"></i>
                    </span>
                <?php endif ?>
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
