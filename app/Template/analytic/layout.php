<?php if ($is_ajax): ?>
    <div class="page-header">
        <h2><?= $title ?></h2>
    </div>
<?php else: ?>
    <?= $this->projectHeader->render($project, 'TaskListController', 'show') ?>
<?php endif ?>
<section class="sidebar-container">
    <?= $this->render($sidebar_template, array('project' => $project)) ?>

    <div class="sidebar-content">
        <?= $content_for_sublayout ?>
    </div>
</section>
