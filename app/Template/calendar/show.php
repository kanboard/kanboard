<section id="main">
    <?= $this->projectHeader->render($project, 'CalendarController', 'show') ?>
    <calendar save-url="<?= $this->url->href('CalendarController', 'save', array('project_id' => $project['id'])) ?>"
              check-url="<?= $this->url->href('CalendarController', 'project', array('project_id' => $project['id'])) ?>">
    </calendar>
</section>
