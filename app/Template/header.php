<?php $_title = $this->render('header/title', array(
    'project' => isset($project) ? $project : null,
    'task' => isset($task) ? $task : null,
    'description' => isset($description) ? $description : null,
    'title' => $title,
)) ?>

<?php $_top_right_corner = implode('&nbsp;', array(
        $this->render('header/user_notifications'),
        $this->render('header/creation_dropdown'),
        $this->render('header/user_dropdown')
    )) ?>

<header>
    <div class="grid grid-reverse grid-noGutter">
        <?php if (! empty($board_selector)): ?>
            <div class="col-1 col_xs-4 col_sm-3 col_md-3 col_lg-2 pull-right">
                <?= $_top_right_corner ?>
            </div>
            <div class="col-2 col_xs-8 col_sm-9 col_md-1 col_lg-1">
                <?= $this->render('header/board_selector', array('board_selector' => $board_selector)) ?>
            </div>
            <div class="col-9 col_xs-12 col_sm-12 col_md-8 col_lg-9">
                <?= $_title ?>
            </div>
        <?php else: ?>
            <div class="col-2 col_xs-12 pull-right">
                <?= $_top_right_corner ?>
            </div>
            <div class="col-10 col_xs-12">
                <?= $_title ?>
            </div>
        <?php endif ?>
    </div>
</header>
