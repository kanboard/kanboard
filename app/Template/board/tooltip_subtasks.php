<section id="tooltip-subtasks">
<?php foreach ($subtasks as $subtask): ?>
    <?= $this->subtask->toggleStatus($subtask, 'board', $task['project_id']) ?>
    <?= $this->e(empty($subtask['username']) ? '' : ' ['.$this->user->getFullname($subtask).']') ?>
    <br/>
<?php endforeach ?>
</section>
