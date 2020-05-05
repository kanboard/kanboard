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
                   data-save-url="<?= $this->url->href('BoardAjaxController', 'save', array('project_id' => $project['id'])) ?>"
                   data-reload-url="<?= $this->url->href('BoardAjaxController', 'reload', array('project_id' => $project['id'])) ?>"
                   data-check-url="<?= $this->url->href('BoardAjaxController', 'check', array('project_id' => $project['id'], 'timestamp' => time())) ?>"
                   data-task-creation-url="<?= $this->url->href('TaskCreationController', 'show', array('project_id' => $project['id'])) ?>"
            >
        <?php endif ?>

        <?php foreach ($swimlanes as $index => $swimlane): ?>
            <?php if (! ($swimlane['nb_tasks'] === 0 && isset($not_editable))): ?>

                <!-- Note: Do not show swimlane row on the top otherwise we can't collapse columns -->
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

                <?php if ($index === 0 && $swimlane['nb_swimlanes'] > 1): ?>
                    <?= $this->render('board/table_swimlane', array(
                        'project' => $project,
                        'swimlane' => $swimlane,
                        'not_editable' => isset($not_editable),
                    )) ?>
                <?php endif ?>

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
