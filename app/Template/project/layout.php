<section id="main">
    <?= $this->projectHeader->render($project, 'TaskListController', 'show') ?>
    <section class="sidebar-container">

        <?= $this->render($sidebar_template, array('project' => $project)) ?>

        <div class="sidebar-content">
            <?= $content_for_sublayout ?>
        </div>
    </section>
</section>
