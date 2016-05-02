<section id="main">
    <?= $this->projectHeader->render($project, 'Gantt', 'project') ?>
    <div class="menu-inline">
        <ul>
            <li <?= $sorting === 'board' ? 'class="active"' : '' ?>>
                <?= $this->url->button('fa-sort-numeric-asc', t('Sort by position'), 'gantt', 'project', array('project_id' => $project['id'], 'sorting' => 'board')) ?>
            </li>
            <li <?= $sorting === 'date' ? 'class="active"' : '' ?>>
                <?= $this->url->button('fa-sort-amount-asc', t('Sort by date'), 'gantt', 'project', array('project_id' => $project['id'], 'sorting' => 'date')) ?>
            </li>
            <li>
                <?= $this->url->button('fa-plus', t('Add task'), 'gantt', 'task', array('project_id' => $project['id']), false, 'popover') ?>
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
        ></div>
        <p class="alert alert-info"><?= t('Moving or resizing a task will change the start and due date of the task.') ?></p>
    <?php else: ?>
        <p class="alert"><?= t('There is no task in your project.') ?></p>
    <?php endif ?>
</section>
