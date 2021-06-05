<div class="page-header">
    <h2><?= t('Edit the board for "%s"', $project['name']) ?></h2>
    <ul>
        <li>
            <?= $this->modal->medium('plus', t('Add a new column'), 'ColumnController', 'create', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
</div>

<?php if (empty($columns)): ?>
    <p class="alert alert-error"><?= t('Your board doesn\'t have any columns!') ?></p>
<?php else: ?>
    <table
        class="columns-table table-striped"
        data-save-position-url="<?= $this->url->href('ColumnController', 'move', array('project_id' => $project['id'], 'csrf_token' => $this->app->getToken()->getReusableCSRFToken())) ?>">
        <thead>
        <tr>
            <th><?= t('Column') ?></th>
            <th class="column-10"><?= t('Task limit') ?></th>
            <th class="column-15"><?= t('Visible on dashboard') ?></th>
            <th class="column-12"><?= t('Open tasks') ?></th>
            <th class="column-12"><?= t('Closed tasks') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($columns as $column): ?>
        <tr data-column-id="<?= $column['id'] ?>">
            <td>
                <i class="fa fa-arrows-alt draggable-row-handle" title="<?= t('Change column position') ?>" role="button" aria-label="<?= t('Change column position') ?>"></i>&nbsp;
                <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li>
                            <?= $this->modal->medium('edit', t('Edit'), 'ColumnController', 'edit', array('project_id' => $project['id'], 'column_id' => $column['id'])) ?>
                        </li>
                        <?php if ($column['nb_open_tasks'] == 0 && $column['nb_closed_tasks'] == 0): ?>
                            <li>
                                <?= $this->modal->confirm('trash-o', t('Remove'), 'ColumnController', 'confirm', array('project_id' => $project['id'], 'column_id' => $column['id'])) ?>
                            </li>
                        <?php endif ?>
                    </ul>
                </div>
                <?= $this->text->e($column['title']) ?>
                <?php if (! empty($column['description'])): ?>
                    <?= $this->app->tooltipMarkdown($column['description']) ?>
                <?php endif ?>
            </td>
            <td>
                <?= $column['task_limit'] ?: '∞' ?>
            </td>
            <td>
                <?= $column['hide_in_dashboard'] == 0 ? t('Yes') : t('No') ?>
            </td>
            <td>
                <?= $column['nb_open_tasks'] ?>
            </td>
            <td>
                <?= $column['nb_closed_tasks'] ?>
            </td>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>
