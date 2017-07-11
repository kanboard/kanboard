<?php if (! empty($task['subtasks'])): ?>
<div class="task-list-subtasks">
    <?php foreach ($task['subtasks'] as $subtask): ?>
        <div class="task-list-subtask">
            <span class="subtask-cell column-50">
                <?= $this->subtask->renderToggleStatus($task, $subtask, 'rows', isset($user_id) ? $user_id : 0) ?>
            </span>
            <span class="subtask-cell column-20 subtask-assignee">
                <?php if (! empty($subtask['username'])): ?>
                    <?= $this->text->e($subtask['name'] ?: $subtask['username']) ?>
                <?php endif ?>
            </span>
            <span class="subtask-cell subtask-time-tracking-cell">
                <?= $this->render('subtask/timer', array(
                    'task'    => $task,
                    'subtask' => $subtask,
                )) ?>
            </span>
        </div>
    <?php endforeach ?>
</div>
<?php endif ?>
