<section id="main">

    <?= $this->projectHeader->render($project, 'Board', 'show', true) ?>

    <?= $this->render('board/table_container', array(
        'project' => $project,
        'swimlanes' => $swimlanes,
        'board_private_refresh_interval' => $board_private_refresh_interval,
        'board_highlight_period' => $board_highlight_period,
    )) ?>

</section>
