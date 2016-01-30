<section id="main">
    <?= $this->render('project/filters', array(
        'project' => $project,
        'filters' => $filters,
        'users_list' => $users_list,
    )) ?>

    <div class="menu-inline">
        <ul>
            <li <?= $sorting === 'board' ? 'class="active"' : '' ?>>
                <i class="fa fa-sort-numeric-asc fa-fw"></i>
                <?= $this->url->link(t('Sort by position'), 'gantt', 'project', array('project_id' => $project['id'], 'sorting' => 'board')) ?>
            </li>
            <li <?= $sorting === 'date' ? 'class="active"' : '' ?>>
                <i class="fa fa-sort-amount-asc fa-fw"></i>
                <?= $this->url->link(t('Sort by date'), 'gantt', 'project', array('project_id' => $project['id'], 'sorting' => 'date')) ?>
            </li>
            <li>
                <i class="fa fa-plus fa-fw"></i>
                <?= $this->url->link(t('Add task'), 'gantt', 'task', array('project_id' => $project['id']), false, 'popover') ?>
            </li>
        </ul>
    </div>

    <?php if (! empty($tasks)): ?>
        <div
            id="gantt-chart"
            data-records='<?= json_encode($tasks, JSON_HEX_APOS) ?>'
            data-save-url="<?= $this->url->href('gantt', 'saveTaskDate', array('project_id' => $project['id'])) ?>"
            data-label-start-date="<?= t('Start date:') ?>"
            data-label-end-date="<?= t('Due date:') ?>"
            data-label-assignee="<?= t('Assignee:') ?>"
            data-label-not-defined="<?= t('There is no start date or due date for this task.') ?>"
            <?php if ($this->user->hasProjectAccess('taskmodification', 'edit', $project['id'])): ?>
                data-taskmodification="1"
                data-label-change-assignee="<?= t('Change assignee') ?>"
                data-url-change-assignee="<?= $this->url->to('BoardPopover', 'changeAssignee', array('project_id' => $project['id'], 'redirect' => 'gantt')) ?>"
                data-label-change-category="<?= t('Change category') ?>"
                data-url-change-category="<?= $this->url->to('BoardPopover', 'changeCategory', array('project_id' => $project['id'], 'redirect' => 'gantt')) ?>"
                data-label-change-description="<?= t('Change description') ?>"
                data-url-change-description="<?= $this->url->to('taskmodification', 'description', array('project_id' => $project['id'], 'redirect' => 'gantt')) ?>"
                data-label-edit-task="<?= t('Edit this task') ?>"
                data-url-edit-task="<?= $this->url->to('taskmodification', 'edit', array('project_id' => $project['id'], 'redirect' => 'gantt')) ?>"
                data-label-add-comment="<?= t('Add a comment') ?>"
                data-url-add-comment="<?= $this->url->to('comment', 'create', array('project_id' => $project['id'], 'redirect' => 'gantt')) ?>"
                data-label-add-link="<?= t('Add a link') ?>"
                data-url-add-link="<?= $this->url->to('tasklink', 'create', array('project_id' => $project['id'], 'redirect' => 'gantt')) ?>"
                data-label-add-screenshot="<?= t('Add a screenshot') ?>"
                data-url-add-screenshot="<?= $this->url->to('BoardPopover', 'screenshot', array('project_id' => $project['id'], 'redirect' => 'gantt')) ?>"
                data-label-close="<?= t('Close this task') ?>"
                data-url-close="<?= $this->url->to('taskstatus', 'close', array('project_id' => $project['id'], 'redirect' => 'gantt')) ?>"
                data-label-open="<?= t('Open this task') ?>"
                data-url-open="<?= $this->url->to('taskstatus', 'open', array('project_id' => $project['id'], 'redirect' => 'gantt')) ?>"
            <?php else: ?>
                data-taskmodification="0"
            <?php endif ?>
        ></div>
        <p class="alert alert-info"><?= t('Moving or resizing a task will change the start and due date of the task.') ?></p>
    <?php else: ?>
        <p class="alert"><?= t('There is no task in your project.') ?></p>
    <?php endif ?>
</section>
