<section id="main">
    <?= $this->render('project/filters', array(
        'project' => $project,
        'filters' => $filters,
    )) ?>

    <div id="calendar"
         data-save-url="<?= $this->url->href('calendar', 'save') ?>"
         data-check-url="<?= $this->url->href('calendar', 'project', array('project_id' => $project['id'])) ?>"
         data-check-interval="<?= $check_interval ?>"
    >
    </div>
</section>