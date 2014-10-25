<div class="page-header">
    <h2><?= t('Automatic actions for the project "%s"', $project['name']) ?></h2>
</div>

<?php if (! empty($actions)): ?>

<h3><?= t('Defined actions') ?></h3>
<table>
    <tr>
        <th><?= t('Event name') ?></th>
        <th><?= t('Action name') ?></th>
        <th><?= t('Action parameters') ?></th>
        <th><?= t('Action') ?></th>
    </tr>

    <?php foreach ($actions as $action): ?>
    <tr>
        <td><?= Helper\in_list($action['event_name'], $available_events) ?></td>
        <td><?= Helper\in_list($action['action_name'], $available_actions) ?></td>
        <td>
            <ul>
            <?php foreach ($action['params'] as $param): ?>
                <li>
                    <?= Helper\in_list($param['name'], $available_params) ?> =
                    <strong>
                    <?php if (Helper\contains($param['name'], 'column_id')): ?>
                        <?= Helper\in_list($param['value'], $columns_list) ?>
                    <?php elseif (Helper\contains($param['name'], 'user_id')): ?>
                        <?= Helper\in_list($param['value'], $users_list) ?>
                    <?php elseif (Helper\contains($param['name'], 'project_id')): ?>
                        <?= Helper\in_list($param['value'], $projects_list) ?>
                    <?php elseif (Helper\contains($param['name'], 'color_id')): ?>
                        <?= Helper\in_list($param['value'], $colors_list) ?>
                    <?php elseif (Helper\contains($param['name'], 'category_id')): ?>
                        <?= Helper\in_list($param['value'], $categories_list) ?>
                    <?php elseif (Helper\contains($param['name'], 'label')): ?>
                        <?= Helper\escape($param['value']) ?>
                    <?php endif ?>
                    </strong>
                </li>
            <?php endforeach ?>
            </ul>
        </td>
        <td>
            <a href="?controller=action&amp;action=confirm&amp;project_id=<?= $project['id'] ?>&amp;action_id=<?= $action['id'] ?>"><?= t('Remove') ?></a>
        </td>
    </tr>
    <?php endforeach ?>

</table>

<?php endif ?>

<h3><?= t('Add an action') ?></h3>
<form method="post" action="?controller=action&amp;action=event&amp;project_id=<?= $project['id'] ?>" autocomplete="off">
    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('project_id', $values) ?>

    <?= Helper\form_label(t('Action'), 'action_name') ?>
    <?= Helper\form_select('action_name', $available_actions, $values) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Next step') ?>" class="btn btn-blue"/>
    </div>
</form>