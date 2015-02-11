<section id="tooltip-subtasks">
<?php foreach ($subtasks as $subtask): ?>
    <?= $this->toggleSubtaskStatus($subtask, 'board') ?>
    <?= $this->e(empty($subtask['username']) ? '' : ' ['.$this->getFullname($subtask).']') ?>
    <br/>
<?php endforeach ?>
</section>
