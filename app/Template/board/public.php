<section id="main" class="public-board">

   <?= $this->render('board/show', array(
            'project' => $project,
            'swimlanes' => $swimlanes,
            'categories_listing' => $categories_listing,
            'categories_description' => $categories_description,
            'board_private_refresh_interval' => $board_private_refresh_interval,
            'board_highlight_period' => $board_highlight_period,
            'not_editable' => true,
    )) ?>

</section>