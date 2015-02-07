<div class="page-header">
    <h2><?= t('Edit the board for "%s"', $project['name']) ?></h2>
</div>

<h3><?= t('Change columns') ?></h3>
<table>
    <tr>
        <th><?= t('Column title') ?></th>
        <th><?= t('Description') ?></th>
        <th><?= t('Task limit') ?></th>
        <th><?= t('Actions') ?></th>
    </tr>
    <?php foreach ($columns as $column): ?>
    <tr>
        <td class="column-30"><?= $this->e($column['title']) ?></td>
        <td><?= $this->e($column['description']) ?></td>
        <td class="column-10"><?= $this->e($column['task_limit']) ?></td>
        <td class="column-20">
            <ul>
                <li>
                    <?= $this->a(t('Edit'), 'board', 'editColumn', array('project_id' => $project['id'], 'column_id' => $column['id'])) ?>
                </li>
                <?php if ($column['position'] != 1): ?>
                <li>
                    <?= $this->a(t('Move Up'), 'board', 'moveColumn', array('project_id' => $project['id'], 'column_id' => $column['id'], 'direction' => 'up'), true) ?>
                </li>
                <?php endif ?>
                <?php if ($column['position'] != count($columns)): ?>
                <li>
                    <?= $this->a(t('Move Down'), 'board', 'moveColumn', array('project_id' => $project['id'], 'column_id' => $column['id'], 'direction' => 'down'), true) ?>
                </li>
                <?php endif ?>
                <li>
                    <?= $this->a(t('Remove'), 'board', 'remove', array('project_id' => $project['id'], 'column_id' => $column['id'])) ?>
                </li>
            </ul>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<h3><?= t('Add a new column') ?></h3>
<form method="post" action="<?= $this->u('board', 'add', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>

    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Title'), 'title') ?>
    <?= $this->formText('title', $values, $errors, array('required', 'maxlength="50"')) ?>
    <?= $this->formLabel(t('Description'), 'description') ?>
    <?= $this->formTextarea('description', $values, $errors) ?>
    <div class="form-help"><a href="http://kanboard.net/documentation/syntax-guide" target="_blank" rel="noreferrer"><?= t('Write your text in Markdown') ?></a></div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Add this column') ?>" class="btn btn-blue"/>
    </div>
</form>