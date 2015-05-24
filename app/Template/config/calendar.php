<div class="page-header">
    <h2><?= t('Calendar settings') ?></h2>
</div>
<section>
<form method="post" action="<?= $this->url->href('config', 'calendar') ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <h3><?= t('Project calendar view') ?></h3>
    <div class="listing">
        <?= $this->form->radios('calendar_project_tasks', array(
                'date_creation' => t('Show tasks based on the creation date'),
                'date_started' => t('Show tasks based on the start date'),
            ), $values) ?>
    </div>

    <h3><?= t('User calendar view') ?></h3>
    <div class="listing">
        <?= $this->form->radios('calendar_user_tasks', array(
                'date_creation' => t('Show tasks based on the creation date'),
                'date_started' => t('Show tasks based on the start date'),
            ), $values) ?>

        <h4><?= t('Subtasks time tracking') ?></h4>
        <?= $this->form->checkbox('calendar_user_subtasks_time_tracking', t('Show subtasks based on the time tracking'), 1, $values['calendar_user_subtasks_time_tracking'] == 1) ?>
        <?= $this->form->checkbox('calendar_user_subtasks_forecast', t('Show subtask estimates (forecast of future work)'), 1, $values['calendar_user_subtasks_forecast'] == 1) ?>
    </div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
</section>