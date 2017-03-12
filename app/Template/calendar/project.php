<?= $this->projectHeader->render($project, 'CalendarController', 'show') ?>

<?= $this->calendar->render(
    $this->url->href('CalendarController', 'projectEvents', array('project_id' => $project['id'])),
    $this->url->href('CalendarController', 'save', array('project_id' => $project['id']))
) ?>
