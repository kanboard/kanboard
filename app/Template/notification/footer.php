<hr/>
Kanboard

<?php if (isset($application_url) && ! empty($application_url)): ?>
    - <a href="<?= $application_url.$this->u('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"><?= t('view the task on Kanboard') ?></a>.
<?php endif ?>
