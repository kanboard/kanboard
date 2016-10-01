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
    <div class="title-container">
        <?= $_title ?>
    </div>
    <?php if (! empty($board_selector)): ?>
    <div class="board-selector-container">
        <?= $this->render('header/board_selector', array('board_selector' => $board_selector)) ?>
    </div>
    <?php endif ?>
    <div class="menus-container pull-right">
        <?= $_top_right_corner ?>
    </div>
</header>
