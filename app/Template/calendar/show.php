<section id="main">
    <?= $this->projectHeader->render($project, 'Calendar', 'show') ?>
    <div id="calendar"
         data-save-url="<?= $this->url->href('calendar', 'save', array('project_id' => $project['id'])) ?>"
         data-check-url="<?= $this->url->href('calendar', 'project', array('project_id' => $project['id'])) ?>"
         data-check-interval="<?= $check_interval ?>"
    >
    </div>
</section>