<div class="page-header">
    <h2><?= t('Edit the board for "%s"', $project['name']) ?></h2>
</div>
<section>

<h3><?= t('Change columns') ?></h3>
<form method="post" action="<?= Helper\u('board', 'update', array('project_id' => $project['id'])) ?>" autocomplete="off">
    <?= Helper\form_csrf() ?>
    <?php $i = 0; ?>
    <table>
        <tr>
            <th><?= t('Position') ?></th>
            <th><?= t('Column title') ?></th>
            <th><?= t('Task limit') ?></th>
            <th><?= t('Actions') ?></th>
        </tr>
        <?php foreach ($columns as $column): ?>
        <tr>
            <td><?= Helper\form_label(++$i, 'title['.$column['id'].']', array('title="column_id='.$column['id'].'"')) ?></td>
            <td><?= Helper\form_text('title['.$column['id'].']', $values, $errors, array('required')) ?></td>
            <td><?= Helper\form_number('task_limit['.$column['id'].']', $values, $errors, array('placeholder="'.t('limit').'"')) ?></td>
            <td>
                <ul>
                    <?php if ($column['position'] != 1): ?>
                    <li>
                        <?= Helper\a(t('Move Up'), 'board', 'moveColumn', array('project_id' => $project['id'], 'column_id' => $column['id'], 'direction' => 'up'), true) ?>
                    </li>
                    <?php endif ?>
                    <?php if ($column['position'] != count($columns)): ?>
                    <li>
                        <?= Helper\a(t('Move Down'), 'board', 'moveColumn', array('project_id' => $project['id'], 'column_id' => $column['id'], 'direction' => 'down'), true) ?>
                    </li>
                    <?php endif ?>
                    <li>
                        <?= Helper\a(t('Remove'), 'board', 'remove', array('project_id' => $project['id'], 'column_id' => $column['id'])) ?>
                    </li>
                </ul>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    <div class="form-actions">
        <input type="submit" value="<?= t('Update') ?>" class="btn btn-blue"/>
    </div>
</form>
<hr/>
<h3><?= t('Add a new column') ?></h3>
<form method="post" action="<?= Helper\u('board', 'add', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= Helper\form_csrf() ?>

    <?= Helper\form_hidden('project_id', $values) ?>

    <?= Helper\form_label(t('Title'), 'title') ?>
    <?= Helper\form_text('title', $values, $errors, array('required')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Add this column') ?>" class="btn btn-blue"/>
    </div>
</form>