<div class="page-header">
    <h2><?= t('Edit the board for "%s"', $project['name']) ?></h2>
    <ul>
        <li>
            <i class="fa fa-plus fa-fw"></i>
            <?= $this->url->link(t('Add a new column'), 'ColumnController', 'create', array('project_id' => $project['id']), false, 'popover') ?>
        </li>
    </ul>
</div>

<?php if (empty($columns)): ?>
    <p class="alert alert-error"><?= t('Your board doesn\'t have any columns!') ?></p>
<?php else: ?>
    <table
        class="columns-table table-striped"
        data-save-position-url="<?= $this->url->href('ColumnController', 'move', array('project_id' => $project['id'])) ?>">
        <thead>
        <tr>
            <th class="column-70"><?= t('Column title') ?></th>
            <th class="column-25"><?= t('Task limit') ?></th>
            <th class="column-5"><?= t('Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($columns as $column): ?>
        <tr data-column-id="<?= $column['id'] ?>">
            <td>
                <i class="fa fa-arrows-alt draggable-row-handle" title="<?= t('Change column position') ?>"></i>
                <?= $this->text->e($column['title']) ?>
                <?php if (! empty($column['description'])): ?>
                    <span class="tooltip" title="<?= $this->text->markdownAttribute($column['description']) ?>">
                        <i class="fa fa-info-circle"></i>
                    </span>
                <?php endif ?>
            </td>
            <td>
                <?= $this->text->e($column['task_limit']) ?>
            </td>
            <td>
                <div class="dropdown">
                <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
                <ul>
                    <li>
                        <i class="fa fa-pencil-square-o fa-fw" aria-hidden="true"></i>
                        <?= $this->url->link(t('Edit'), 'ColumnController', 'edit', array('project_id' => $project['id'], 'column_id' => $column['id']), false, 'popover') ?>
                    </li>
                    <li>
                        <i class="fa fa-trash-o fa-fw" aria-hidden="true"></i>
                        <?= $this->url->link(t('Remove'), 'ColumnController', 'confirm', array('project_id' => $project['id'], 'column_id' => $column['id']), false, 'popover') ?>
                    </li>
                </ul>
                </div>
            </td>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>
