<section id="tooltip-subtasks">
<?php foreach ($subtasks as $subtask): ?>
    <?= $this->a(
        trim($this->render('subtask/icons', array('subtask' => $subtask))) . $this->e($subtask['title']),
        'board',
        'toggleSubtask',
        array('task_id' => $subtask['task_id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])
    ) ?>

    <?= $this->e(empty($subtask['username']) ? '' : ' ['.$this->getFullname($subtask).']') ?>

    <br/>
<?php endforeach ?>
</section>
