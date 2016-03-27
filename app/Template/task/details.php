<section id="task-summary">
    <h2><?= $this->text->e($task['title']) ?></h2>

    <div class="task-summary-container color-<?= $task['color_id'] ?>">
        <div class="task-summary-column">
            <ul class="no-bullet">
                <li>
                    <strong><?= t('Status:') ?></strong>
                    <span>
                    <?php if ($task['is_active'] == 1): ?>
                        <?= t('open') ?>
                    <?php else: ?>
                        <?= t('closed') ?>
                    <?php endif ?>
                    </span>
                </li>
                <li>
                    <strong><?= t('Priority:') ?></strong> <span><?= $task['priority'] ?></span>
                </li>
                <?php if (! empty($task['reference'])): ?>
                    <li>
                        <strong><?= t('Reference:') ?></strong> <span><?= $this->text->e($task['reference']) ?></span>
                    </li>
                <?php endif ?>
                <?php if (! empty($task['score'])): ?>
                    <li>
                        <strong><?= t('Complexity:') ?></strong> <span><?= $this->text->e($task['score']) ?></span>
                    </li>
                <?php endif ?>
                <?php if ($project['is_public']): ?>
                <li class="smaller">
                    <i class="fa fa-external-link fa-fw"></i>
                    <?= $this->url->link(t('Public link'), 'task', 'readonly', array('task_id' => $task['id'], 'token' => $project['token']), false, '', '', true) ?>
                </li>
                <?php endif ?>
                <?php if ($project['is_public'] && !$editable): ?>
                <li class="smaller">
                    <i class="fa fa-th fa-fw"></i>
                    <?= $this->url->link(t('Back to the board'), 'board', 'readonly', array('token' => $project['token'])) ?>
                </li>
                <?php endif ?>
                <li class="smaller">
            </ul>
        </div>
        <div class="task-summary-column">
            <ul class="no-bullet">
                <?php if (! empty($task['category_name'])): ?>
                    <li>
                        <strong><?= t('Category:') ?></strong>
                        <span><?= $this->text->e($task['category_name']) ?></span>
                    </li>
                <?php endif ?>
                <?php if (! empty($task['swimlane_name'])): ?>
                    <li>
                        <strong><?= t('Swimlane:') ?></strong>
                        <span><?= $this->text->e($task['swimlane_name']) ?></span>
                    </li>
                <?php endif ?>
                <li>
                    <strong><?= t('Column:') ?></strong>
                    <span><?= $this->text->e($task['column_title']) ?></span>
                </li>
                <li>
                    <strong><?= t('Position:') ?></strong>
                    <span><?= $task['position'] ?></span>
                </li>
            </ul>
        </div>
        <div class="task-summary-column">
            <ul class="no-bullet">
                <li>
                    <strong><?= t('Assignee:') ?></strong>
                    <span>
                    <?php if ($task['assignee_username']): ?>
                        <?= $this->text->e($task['assignee_name'] ?: $task['assignee_username']) ?>
                    <?php else: ?>
                        <?= t('not assigned') ?>
                    <?php endif ?>
                    </span>
                </li>
                <?php if ($task['creator_username']): ?>
                    <li>
                        <strong><?= t('Creator:') ?></strong>
                        <span><?= $this->text->e($task['creator_name'] ?: $task['creator_username']) ?></span>
                    </li>
                <?php endif ?>
                <?php if ($task['date_due']): ?>
                <li>
                    <strong><?= t('Due date:') ?></strong>
                    <span><?= $this->dt->date($task['date_due']) ?></span>
                </li>
                <?php endif ?>
                <?php if ($task['time_estimated']): ?>
                <li>
                    <strong><?= t('Time estimated:') ?></strong>
                    <span><?= t('%s hours', $task['time_estimated']) ?></span>
                </li>
                <?php endif ?>
                <?php if ($task['time_spent']): ?>
                <li>
                    <strong><?= t('Time spent:') ?></strong>
                    <span><?= t('%s hours', $task['time_spent']) ?></span>
                </li>
                <?php endif ?>
            </ul>
        </div>
        <div class="task-summary-column">
            <ul class="no-bullet">
                <li>
                    <strong><?= t('Created:') ?></strong>
                    <span><?= $this->dt->datetime($task['date_creation']) ?></span>
                </li>
                <li>
                    <strong><?= t('Modified:') ?></strong>
                    <span><?= $this->dt->datetime($task['date_modification']) ?></span>
                </li>
                <?php if ($task['date_completed']): ?>
                <li>
                    <strong><?= t('Completed:') ?></strong>
                    <span><?= $this->dt->datetime($task['date_completed']) ?></span>
                </li>
                <?php endif ?>
                <?php if ($task['date_started']): ?>
                <li>
                    <strong><?= t('Started:') ?></strong>
                    <span><?= $this->dt->datetime($task['date_started']) ?></span>
                </li>
                <?php endif ?>
                <?php if ($task['date_moved']): ?>
                <li>
                    <strong><?= t('Moved:') ?></strong>
                    <span><?= $this->dt->datetime($task['date_moved']) ?></span>
                </li>
                <?php endif ?>
            </ul>
        </div>
    </div>

    <?php if ($editable && empty($task['date_started'])): ?>
        <div class="task-summary-buttons">
            <?= $this->url->button('fa-play', t('Set start date'), 'taskmodification', 'start', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </div>
    <?php endif ?>
</section>
