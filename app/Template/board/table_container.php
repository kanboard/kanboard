<div id="board-container"
     class="<?= ($project['task_limit'] && array_key_exists('nb_active_tasks', $project) && $project['nb_active_tasks'] > $project['task_limit']) ? 'board-task-list-limit' : '' ?>">
    <?php if (empty($swimlanes) || empty($swimlanes[0]['nb_columns'])): ?>
        <p class="alert alert-error"><?= t('There is no column or swimlane activated in your project!') ?></p>
    <?php else: ?>

        <?php if (isset($not_editable)): ?>
            <table id="board" class="board-project-<?= $project['id'] ?>">
        <?php else: ?>
            <table id="board"
                   class="board-project-<?= $project['id'] ?>"
                   data-project-id="<?= $project['id'] ?>"
                   data-check-interval="<?= $board_private_refresh_interval ?>"
                   data-save-url="<?= $this->url->href('BoardAjaxController', 'save', array('project_id' => $project['id'], 'csrf_token' => $this->app->getToken()->getReusableCSRFToken())) ?>"
                   data-reload-url="<?= $this->url->href('BoardAjaxController', 'reload', array('project_id' => $project['id'], 'csrf_token' => $this->app->getToken()->getReusableCSRFToken())) ?>"
                   data-check-url="<?= $this->url->href('BoardAjaxController', 'check', array('project_id' => $project['id'], 'timestamp' => time(), 'csrf_token' => $this->app->getToken()->getReusableCSRFToken())) ?>"
                   data-task-creation-url="<?= $this->url->href('TaskCreationController', 'show', array('project_id' => $project['id'])) ?>"
            >
        <?php endif ?>

        <?php foreach ($swimlanes as $index => $swimlane): ?>
            <?php if (! ($swimlane['nb_tasks'] === 0 && isset($not_editable))): ?>

                <?php if ($index === 0 && $swimlane['nb_swimlanes'] > 1): ?>
                    <!-- Render empty columns to setup the "grid" for collapsing columns (Only once and only if more than 1 swimlane in project) -->
                    <?= $this->render('board/table_column_first', array(
                        'swimlane' => $swimlane,
                        'not_editable' => isset($not_editable),
                    )) ?>
                <?php endif ?>

                <?php if ($index === 0 && $swimlane['nb_swimlanes'] > 1): ?>
                    <!-- Only show first swimlane-header if project more than 1 swimlanes -->
                    <?= $this->render('board/table_swimlane', array(
                        'project' => $project,
                        'swimlane' => $swimlane,
                        'not_editable' => isset($not_editable),
                    )) ?>
                <?php endif ?>

                <?php if ($index > 0 && $swimlane['nb_swimlanes'] > 1): ?>
                    <?= $this->render('board/table_swimlane', array(
                        'project' => $project,
                        'swimlane' => $swimlane,
                        'not_editable' => isset($not_editable),
                    )) ?>
                <?php endif ?>

                <?= $this->render('board/table_column', array(
                    'swimlane' => $swimlane,
                    'not_editable' => isset($not_editable),
                )) ?>

                <?= $this->render('board/table_tasks', array(
                    'project' => $project,
                    'swimlane' => $swimlane,
                    'not_editable' => isset($not_editable),
                    'board_highlight_period' => $board_highlight_period,
                )) ?>

            <?php endif ?>
        <?php endforeach ?>

        </table>

    <?php endif ?>
</div>
