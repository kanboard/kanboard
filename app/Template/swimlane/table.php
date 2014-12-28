<table>
    <tr>
        <?php if (! isset($hide_position)): ?>
            <th><?= t('Position') ?></th>
        <?php endif ?>
        <th class="column-60"><?= t('Name') ?></th>
        <th class="column-35"><?= t('Actions') ?></th>
    </tr>
    <?php foreach ($swimlanes as $swimlane): ?>
    <tr>
        <?php if (! isset($hide_position)): ?>
            <td>#<?= $swimlane['position'] ?></td>
        <?php endif ?>
        <td><?= $this->e($swimlane['name']) ?></td>
        <td>
            <ul>
                <?php if ($swimlane['position'] != 0 && $swimlane['position'] != 1): ?>
                    <li>
                        <?= $this->a(t('Move Up'), 'swimlane', 'moveup', array('project_id' => $project['id'], 'swimlane_id' => $swimlane['id']), true) ?>
                    </li>
                <?php endif ?>
                <?php if ($swimlane['position'] != 0 && $swimlane['position'] != count($swimlanes)): ?>
                    <li>
                        <?= $this->a(t('Move Down'), 'swimlane', 'movedown', array('project_id' => $project['id'], 'swimlane_id' => $swimlane['id']), true) ?>
                    </li>
                <?php endif ?>
                <li>
                    <?= $this->a(t('Rename'), 'swimlane', 'edit', array('project_id' => $project['id'], 'swimlane_id' => $swimlane['id'])) ?>
                </li>
                <li>
                    <?php if ($swimlane['is_active']): ?>
                        <?= $this->a(t('Disable'), 'swimlane', 'disable', array('project_id' => $project['id'], 'swimlane_id' => $swimlane['id']), true) ?>
                    <?php else: ?>
                        <?= $this->a(t('Enable'), 'swimlane', 'enable', array('project_id' => $project['id'], 'swimlane_id' => $swimlane['id']), true) ?>
                    <?php endif ?>
                </li>
                <li>
                    <?= $this->a(t('Remove'), 'swimlane', 'confirm', array('project_id' => $project['id'], 'swimlane_id' => $swimlane['id'])) ?>
                </li>
            </ul>
        </td>
    </tr>
    <?php endforeach ?>
</table>