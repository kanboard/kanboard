<div class="page-header">
    <h2><?= t('Automatic actions for the project "%s"', $project['name']) ?></h2>
</div>

<?php if (! empty($actions)): ?>

<h3><?= t('Defined actions') ?></h3>
<table>
    <tr>
        <th><?= t('Automatic actions') ?></th>
        <th><?= t('Action parameters') ?></th>
        <th><?= t('Action') ?></th>
    </tr>

    <?php foreach ($actions as $action): ?>
    <tr>
        <td>
            <ul>
                <li>
                    <?= t('Event name') ?> =
                    <strong><?= $this->text->in($action['event_name'], $available_events) ?></strong>
                </li>
                <li>
                    <?= t('Action name') ?> =
                    <strong><?= $this->text->in($action['action_name'], $available_actions) ?></strong>
                </li>
            <ul>
        </td>
        <td>
            <ul>
            <?php foreach ($action['params'] as $param): ?>
                <li>
                    <?= $this->text->in($param['name'], $available_params) ?> =
                    <strong>
                    <?php if ($this->text->contains($param['name'], 'column_id')): ?>
                        <?= $this->text->in($param['value'], $columns_list) ?>
                    <?php elseif ($this->text->contains($param['name'], 'user_id')): ?>
                        <?= $this->text->in($param['value'], $users_list) ?>
                    <?php elseif ($this->text->contains($param['name'], 'project_id')): ?>
                        <?= $this->text->in($param['value'], $projects_list) ?>
                    <?php elseif ($this->text->contains($param['name'], 'color_id')): ?>
                        <?= $this->text->in($param['value'], $colors_list) ?>
                    <?php elseif ($this->text->contains($param['name'], 'category_id')): ?>
                        <?= $this->text->in($param['value'], $categories_list) ?>
                    <?php elseif ($this->text->contains($param['name'], 'link_id')): ?>
                        <?= $this->text->in($param['value'], $links_list) ?>
                    <?php else: ?>
                        <?= $this->e($param['value']) ?>
                    <?php endif ?>
                    </strong>
                </li>
            <?php endforeach ?>
            </ul>
        </td>
        <td>
            <?= $this->url->link(t('Remove'), 'action', 'confirm', array('project_id' => $project['id'], 'action_id' => $action['id'])) ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<?php endif ?>

<h3><?= t('Add an action') ?></h3>
<form method="post" action="<?= $this->url->href('action', 'event', array('project_id' => $project['id'])) ?>" class="listing">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Action'), 'action_name') ?>
    <?= $this->form->select('action_name', $available_actions, $values) ?><br/>

    <div class="form-actions">
        <input type="submit" value="<?= t('Next step') ?>" class="btn btn-blue"/>
    </div>
</form>