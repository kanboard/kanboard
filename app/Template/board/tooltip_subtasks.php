<section id="tooltip-subtasks">
<?php foreach ($subtasks as $subtask): ?>
    <?= $this->subtask->toggleStatus($subtask, 'board') ?>
    <?= $this->e(empty($subtask['username']) ? '' : ' ['.$this->user->getFullname($subtask).']') ?>
    <br/>
<?php endforeach ?>
</section>
