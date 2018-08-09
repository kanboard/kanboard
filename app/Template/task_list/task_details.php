<div class="table-list-details">
    <?= $this->text->e($task['project_name']) ?> &gt;
    <?= $this->text->e($task['swimlane_name']) ?> &gt;
    <?= $this->text->e($task['column_name']) ?>

    <?php if (! empty($task['category_id'])): ?>
        <span class="table-list-category <?= $task['category_color_id'] ? "color-{$task['category_color_id']}" : '' ?>">
            <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
                <?= $this->url->link(
                    $this->text->e($task['category_name']),
                    'TaskModificationController',
                    'edit',
                    array('task_id' => $task['id'], 'project_id' => $task['project_id']),
                    false,
                    'js-modal-medium' . (! empty($task['category_description']) ? ' tooltip' : ''),
                    t('Change category')
                ) ?>
                <?php if (! empty($task['category_description'])): ?>
                    <?= $this->app->tooltipMarkdown($task['category_description']) ?>
                <?php endif ?>
            <?php else: ?>
                <?= $this->text->e($task['category_name']) ?>
            <?php endif ?>
        </span>
    <?php endif ?>

    <?php foreach ($task['tags'] as $tag): ?>
        <span class="table-list-category task-list-tag <?= $tag['color_id'] ? "color-{$tag['color_id']}" : '' ?>">
            <?= $this->text->e($tag['name']) ?>
        </span>
    <?php endforeach ?>
</div>
