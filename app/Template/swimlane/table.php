<table
    class="swimlanes-table table-striped table-scrolling"
    data-save-position-url="<?= $this->url->href('SwimlaneController', 'move', array('project_id' => $project['id'])) ?>">
    <thead>
        <tr>
            <th><?= t('Name') ?></th>
            <th class="column-15"><?= t('Task limit') ?></th>
            <th class="column-15"><?= t('Open tasks') ?></th>
            <th class="column-15"><?= t('Closed tasks') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($swimlanes as $swimlane): ?>
        <tr data-swimlane-id="<?= $swimlane['id'] ?>">
            <td>
                <?php if (! isset($disable_handle)): ?>
                    <i class="fa fa-arrows-alt draggable-row-handle" title="<?= t('Change column position') ?>"></i>&nbsp;
                <?php endif ?>

                <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li>
                            <?= $this->modal->medium('edit', t('Edit'), 'SwimlaneController', 'edit', array('project_id' => $project['id'], 'swimlane_id' => $swimlane['id'])) ?>
                        </li>
                        <li>
                            <?php if ($swimlane['is_active']): ?>
                                <?= $this->url->icon('toggle-off', t('Disable'), 'SwimlaneController', 'disable', array('project_id' => $project['id'], 'swimlane_id' => $swimlane['id']), true) ?>
                            <?php else: ?>
                                <?= $this->url->icon('toggle-on', t('Enable'), 'SwimlaneController', 'enable', array('project_id' => $project['id'], 'swimlane_id' => $swimlane['id']), true) ?>
                            <?php endif ?>
                        </li>
                        <?php if ($swimlane['nb_open_tasks'] == 0 && $swimlane['nb_closed_tasks'] == 0): ?>
                            <li>
                                <?= $this->modal->confirm('trash-o', t('Remove'), 'SwimlaneController', 'confirm', array('project_id' => $project['id'], 'swimlane_id' => $swimlane['id'])) ?>
                            </li>
                        <?php endif ?>
                    </ul>
                </div>

                <?= $this->text->e($swimlane['name']) ?>

                <?php if (! empty($swimlane['description'])): ?>
                    <?= $this->app->tooltipMarkdown($swimlane['description']) ?>
                <?php endif ?>
            </td>
            <td>
                <?= $swimlane['task_limit'] > 0 ? $swimlane['task_limit'] : '∞' ?>
            </td>
            <td>
                <?= $swimlane['nb_open_tasks'] ?>
            </td>
            <td>
                <?= $swimlane['nb_closed_tasks'] ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
