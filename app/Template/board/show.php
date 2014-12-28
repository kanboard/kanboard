
<?php if (isset($not_editable)): ?>
    <table id="board">
<?php else: ?>
    <table id="board"
           data-project-id="<?= $project['id'] ?>"
           data-check-interval="<?= $board_private_refresh_interval ?>"
           data-save-url="<?= $this->u('board', 'save', array('project_id' => $project['id'])) ?>"
           data-check-url="<?= $this->u('board', 'check', array('project_id' => $project['id'], 'timestamp' => time())) ?>"
    >
<?php endif ?>

<?php foreach ($swimlanes as $swimlane): ?>
    <?php if (empty($swimlane['columns'])): ?>
        <p class="alert alert-error"><?= t('There is no column in your project!') ?></p>
        <?php break ?>
    <?php else: ?>
        <?= $this->render('board/swimlane', array(
            'project' => $project,
            'swimlane' => $swimlane,
            'board_highlight_period' => $board_highlight_period,
            'categories' => $categories,
            'hide_swimlane' => count($swimlanes) === 1,
            'not_editable' => isset($not_editable),
        )) ?>
    <?php endif ?>
<?php endforeach ?>
</table>
