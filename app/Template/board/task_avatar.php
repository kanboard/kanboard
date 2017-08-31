<?php if (! empty($task['owner_id']) || (! empty($task['assignees']))): ?>
<div class="task-board-avatars">
    <span
        <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
        class="task-board-assignee task-board-change-assignee"
        data-url="<?= $this->url->href('TaskModificationController', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>">
    <?php else: ?>
        class="task-board-assignee">
    <?php endif ?>
        <?= $this->avatar->smallMultiple(
            $task['id'],
            'avatar-inline'
        ) ?>
    </span>
</div>
<?php endif ?>
