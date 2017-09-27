<div class="page-header">
    <h2><?= t('Do you really want to remove all tasks of this column?') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('BoardPopoverController', 'removeColumnTasks', array('project_id' => $project['id'])) ?>">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('column_id', $values) ?>
    <?= $this->form->hidden('swimlane_id', $values) ?>

    <p class="alert"><?= t('%d task(s) in the column "%s" and the swimlane "%s" will be permanently removed.', $nb_tasks, $column, $swimlane) ?><br/>
		<span style="font-weight: bold;"><?= t('NOTE: This action can not be undone!') ?></div>

</p>

    <?= $this->modal->submitButtons(array(
        'submitLabel' => t('Yes'),
        'color' => 'red',
    )) ?>
</form>
