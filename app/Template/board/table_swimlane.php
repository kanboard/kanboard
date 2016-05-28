<!-- swimlane -->
<tr id="swimlane-<?= $swimlane['id'] ?>">
   <th class="board-swimlane-header" colspan="<?= $swimlane['nb_columns'] ?>">
        <?php if (! $not_editable): ?>
            <a href="#" class="board-swimlane-toggle" data-swimlane-id="<?= $swimlane['id'] ?>">
                <i class="fa fa-chevron-circle-up hide-icon-swimlane-<?= $swimlane['id'] ?>" title="<?= t('Collapse swimlane') ?>"></i>
                <i class="fa fa-chevron-circle-down show-icon-swimlane-<?= $swimlane['id'] ?>" title="<?= t('Expand swimlane') ?>" style="display: none"></i>
            </a>
        <?php endif ?>

        <?= $this->text->e($swimlane['name']) ?>

        <?php if (! $not_editable && ! empty($swimlane['description'])): ?>
            <span
                title="<?= t('Description') ?>"
                class="tooltip"
                data-href="<?= $this->url->href('BoardTooltipController', 'swimlane', array('swimlane_id' => $swimlane['id'], 'project_id' => $project['id'])) ?>">
                <i class="fa fa-info-circle"></i>
            </span>
        <?php endif ?>

        <span title="<?= t('Task count') ?>" class="board-column-header-task-count swimlane-task-count-<?= $swimlane['id'] ?>">
            (<?= $swimlane['nb_tasks'] ?>)
        </span>
    </th>
</tr>
