<div>
    <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
        <?= $this->render('task/dropdown', array('task' => $task)) ?>
    <?php else: ?>
        <strong><?= '#'.$task['id'] ?></strong>
    <?php endif ?>

    <span class="table-list-title <?= $task['is_active'] == 0 ? 'status-closed' : '' ?>">
        <?= $this->url->link($this->text->e($task['title']), 'TaskViewController', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])) ?>
    </span>
</div>
