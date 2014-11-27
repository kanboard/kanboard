<hr/>
Kanboard

<?php if (isset($application_url) && ! empty($application_url)): ?>
    - <a href="<?= $application_url.'?controller=task&action=show&task_id='.$task['id'] ?>"><?= t('view the task on Kanboard') ?></a>.
<?php endif ?>
