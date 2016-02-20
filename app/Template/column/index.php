<div class="page-header">
    <h2><?= t('Edit the board for "%s"', $project['name']) ?></h2>
    <ul>
        <li>
            <i class="fa fa-plus fa-fw"></i>
            <?= $this->url->link(t('Add a new column'), 'Column', 'create', array('project_id' => $project['id']), false, 'popover') ?>
        </li>
    </ul>
</div>

<?php if (! empty($columns)): ?>

    <?php $first_position = $columns[0]['position']; ?>
    <?php $last_position = $columns[count($columns) - 1]['position']; ?>

    <h3><?= t('Change columns') ?></h3>
    <table>
        <tr>
            <th class="column-70"><?= t('Column title') ?></th>
            <th class="column-25"><?= t('Task limit') ?></th>
            <th class="column-5"><?= t('Actions') ?></th>
        </tr>
        <?php foreach ($columns as $column): ?>
        <tr>
            <td><?= $this->e($column['title']) ?>
             <?php if (! empty($column['description'])): ?>
                <span class="tooltip" title='<?= $this->e($this->text->markdown($column['description'])) ?>'>
                    <i class="fa fa-info-circle"></i>
                </span>
            <?php endif ?>
            </td>
            <td><?= $this->e($column['task_limit']) ?></td>
            <td>
                <div class="dropdown">
                <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
                <ul>
                    <li>
                        <?= $this->url->link(t('Edit'), 'column', 'edit', array('project_id' => $project['id'], 'column_id' => $column['id']), false, 'popover') ?>
                    </li>
                    <?php if ($column['position'] != $first_position): ?>
                    <li>
                        <?= $this->url->link(t('Move Up'), 'column', 'move', array('project_id' => $project['id'], 'column_id' => $column['id'], 'direction' => 'up'), true) ?>
                    </li>
                    <?php endif ?>
                    <?php if ($column['position'] != $last_position): ?>
                    <li>
                        <?= $this->url->link(t('Move Down'), 'column', 'move', array('project_id' => $project['id'], 'column_id' => $column['id'], 'direction' => 'down'), true) ?>
                    </li>
                    <?php endif ?>
                    <li>
                        <?= $this->url->link(t('Remove'), 'column', 'confirm', array('project_id' => $project['id'], 'column_id' => $column['id']), false, 'popover') ?>
                    </li>
                </ul>
                </div>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

<?php endif ?>
