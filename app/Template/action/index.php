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
        <td><?= $this->inList($action['event_name'], $available_events) ?></td>
        <td><?= $this->inList($action['action_name'], $available_actions) ?></td>
        <td>
            <ul>
            <?php foreach ($action['params'] as $param): ?>
                <li>
                    <?= $this->inList($param['name'], $available_params) ?> =
                    <strong>
                    <?php if ($this->contains($param['name'], 'column_id')): ?>
                        <?= $this->inList($param['value'], $columns_list) ?>
                    <?php elseif ($this->contains($param['name'], 'user_id')): ?>
                        <?= $this->inList($param['value'], $users_list) ?>
                    <?php elseif ($this->contains($param['name'], 'project_id')): ?>
                        <?= $this->inList($param['value'], $projects_list) ?>
                    <?php elseif ($this->contains($param['name'], 'color_id')): ?>
                        <?= $this->inList($param['value'], $colors_list) ?>
                    <?php elseif ($this->contains($param['name'], 'category_id')): ?>
                        <?= $this->inList($param['value'], $categories_list) ?>
                    <?php elseif ($this->contains($param['name'], 'label')): ?>
                        <?= $this->e($param['value']) ?>
                    <?php endif ?>
                    </strong>
                </li>
            <?php endforeach ?>
            </ul>
        </td>
        <td>
            <?= $this->a(t('Remove'), 'action', 'confirm', array('project_id' => $project['id'], 'action_id' => $action['id'])) ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<?php endif ?>

<h3><?= t('Add an action') ?></h3>
<form method="post" action="<?= $this->u('action', 'event', array('project_id' => $project['id'])) ?>" class="listing">
    <?= $this->formCsrf() ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Action'), 'action_name') ?>
    <?= $this->formSelect('action_name', $available_actions, $values) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Next step') ?>" class="btn btn-blue"/>
    </div>
</form>