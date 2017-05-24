<section id="main" class="public-board">

    <?php if (empty($nb_active_tasks)): ?>
        <p class="alert alert-warning"><?= t('This project does not have any active tasks!') ?></p>
    <?php endif ?>
    <?= $this->render('board/table_container', array(
            'project' => $project,
            'swimlanes' => $swimlanes,
            'board_private_refresh_interval' => $board_private_refresh_interval,
            'board_highlight_period' => $board_highlight_period,
            'not_editable' => true,
    )) ?>

</section>
