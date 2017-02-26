<div class="table-list-details">
    <?= $this->text->e($task['project_name']) ?> &gt;
    <?= $this->text->e($task['swimlane_name']) ?> &gt;
    <?= $this->text->e($task['column_name']) ?>

    <?php if (! empty($task['category_id'])): ?>
        <span class="table-list-category">
            <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
                <?= $this->url->link(
                    $this->text->e($task['category_name']),
                    'TaskModificationController',
                    'edit',
                    array('task_id' => $task['id'], 'project_id' => $task['project_id']),
                    false,
                    'js-modal-medium' . (! empty($task['category_description']) ? ' tooltip' : ''),
                    ! empty($task['category_description']) ? $this->text->markdownAttribute($task['category_description']) : t('Change category')
                ) ?>
            <?php else: ?>
                <?= $this->text->e($task['category_name']) ?>
            <?php endif ?>
        </span>
    <?php endif ?>

    <?php foreach ($task['tags'] as $tag): ?>
        <span class="table-list-category task-list-tag">
            <?= $this->text->e($tag['name']) ?>
        </span>
    <?php endforeach ?>
</div>
