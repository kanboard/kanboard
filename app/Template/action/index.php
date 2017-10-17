<div class="page-header">
    <h2><?= t('Automatic actions for the project "%s"', $project['name']) ?></h2>
    <ul>
        <li>
            <?= $this->modal->medium('plus', t('Add a new action'), 'ActionCreationController', 'create', array('project_id' => $project['id'])) ?>
        </li>
        <li>
            <?= $this->modal->medium('copy', t('Import from another project'), 'ProjectActionDuplicationController', 'show', array('project_id' => $project['id'])) ?>
        </li>
    </ul>
</div>

<?php if (empty($actions)): ?>
    <p class="alert"><?= t('There is no action at the moment.') ?></p>
<?php else: ?>
    <table class="table-scrolling">
        <?php foreach ($actions as $action): ?>
        <tr>
            <th>
                <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li>
                            <?= $this->modal->confirm('trash-o', t('Remove'), 'ActionController', 'confirm', array('project_id' => $project['id'], 'action_id' => $action['id'])) ?>
                        </li>
                    </ul>
                </div>

                <?php if (! isset($available_params[$action['action_name']])): ?>
                    <?= $this->text->e($action['action_name']) ?>
                <?php else: ?>
                    <?= $this->text->in($action['action_name'], $available_actions) ?>
                <?php endif ?>
            </th>
        </tr>
        <tr>
            <td>
                <?php if (! isset($available_params[$action['action_name']])): ?>
                    <p class="alert alert-error"><?= t('Automatic action not found: "%s"', $action['action_name']) ?></p>
                <?php else: ?>
                <ul>
                    <li>
                        <?= t('Event name') ?> =
                        <strong><?= $this->text->in($action['event_name'], $available_events) ?></strong>
                    </li>
                    <?php foreach ($action['params'] as $param_name => $param_value): ?>
                        <li>
                            <?php if (isset($available_params[$action['action_name']][$param_name]) && is_array($available_params[$action['action_name']][$param_name])): ?>
                                <?= $this->text->e(ucfirst($param_name)) ?> =
                            <?php else: ?>
                                <?= $this->text->in($param_name, $available_params[$action['action_name']]) ?> =
                            <?php endif ?>
                            <strong>
                                <?php if ($this->text->contains($param_name, 'column_id')): ?>
                                    <?= $this->text->in($param_value, $columns_list) ?>
                                <?php elseif ($this->text->contains($param_name, 'user_id')): ?>
                                    <?= $this->text->in($param_value, $users_list) ?>
                                <?php elseif ($this->text->contains($param_name, 'project_id')): ?>
                                    <?= $this->text->in($param_value, $projects_list) ?>
                                <?php elseif ($this->text->contains($param_name, 'color_id')): ?>
                                    <?= $this->text->in($param_value, $colors_list) ?>
                                <?php elseif ($this->text->contains($param_name, 'category_id')): ?>
                                    <?= $this->text->in($param_value, $categories_list) ?>
                                <?php elseif ($this->text->contains($param_name, 'link_id')): ?>
                                    <?= $this->text->in($param_value, $links_list) ?>
                                <?php elseif ($this->text->contains($param_name, 'swimlane_id')): ?>
                                    <?= $this->text->in($param_value, $swimlane_list) ?>
                                <?php else: ?>
                                    <?= $this->text->e($param_value) ?>
                                <?php endif ?>
                            </strong>
                        </li>
                    <?php endforeach ?>
                </ul>
                <?php endif ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
