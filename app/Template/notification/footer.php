<hr/>
Kanboard

<?php if (isset($application_url) && ! empty($application_url)): ?>
    - <a href="<?= $this->url->href('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, '', true) ?>"><?= t('view the task on Kanboard') ?></a>
    - <a href="<?= $this->url->href('board', 'show', array('project_id' => $task['project_id']), false, '', true) ?>"><?= t('view the board on Kanboard') ?></a>
<?php endif ?>
