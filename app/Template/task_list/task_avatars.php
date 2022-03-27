<?php if (! empty($task['owner_id'])): ?>
    <div class="task-list-avatars">
        <span
            <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
            class="task-board-change-assignee"
            data-url="<?= $this->url->href('TaskModificationController', 'edit', array('task_id' => $task['id'])) ?>">
        <?php else: ?>
            class="task-board-assignee">
        <?php endif ?>
            <?= $this->avatar->small(
                $task['owner_id'],
                $task['assignee_username'],
                $task['assignee_name'],
                $task['assignee_email'],
                $task['assignee_avatar_path'],
                'avatar-inline'
            ) ?><span class="task-avatar-assignee"><?= $this->text->e($task['assignee_name'] ?: $task['assignee_username']) ?></span>
        </span>
    </div>
<?php endif ?>
