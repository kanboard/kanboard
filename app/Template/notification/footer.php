<hr/>
Kanboard

<?php if ($this->app->config('application_url') != ''): ?>
    <?php if (isset($task['id'])): ?>
        - <?= $this->url->absoluteLink(t('view the task on Kanboard'), 'TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
    <?php endif ?>
    - <?= $this->url->absoluteLink(t('view the board on Kanboard'), 'BoardViewController', 'show', array('project_id' => $task['project_id'])) ?>
<?php endif ?>
