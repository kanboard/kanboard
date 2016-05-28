<section id="main">
    <?= $this->projectHeader->render($project, 'CalendarController', 'show') ?>
    <div id="calendar"
         data-save-url="<?= $this->url->href('CalendarController', 'save', array('project_id' => $project['id'])) ?>"
         data-check-url="<?= $this->url->href('CalendarController', 'project', array('project_id' => $project['id'])) ?>"
         data-check-interval="<?= $check_interval ?>"
    >
    </div>
</section>
