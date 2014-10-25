<div class="task-show-sidebar">
    <h2><?= t('Actions') ?></h2>
    <div class="task-show-actions">
        <ul>
            <li><a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>"><?= t('Summary') ?></a></li>
            <li><a href="?controller=task&amp;action=edit&amp;task_id=<?= $task['id'] ?>"><?= t('Edit the task') ?></a></li>
            <li><a href="?controller=task&amp;action=description&amp;task_id=<?= $task['id'] ?>"><?= t('Edit the description') ?></a></li>
            <li><a href="?controller=subtask&amp;action=create&amp;task_id=<?= $task['id'] ?>"><?= t('Add a sub-task') ?></a></li>
            <li><a href="?controller=comment&amp;action=create&amp;task_id=<?= $task['id'] ?>"><?= t('Add a comment') ?></a></li>
            <li><a href="?controller=file&amp;action=create&amp;task_id=<?= $task['id'] ?>"><?= t('Attach a document') ?></a></li>
            <li><a href="?controller=task&amp;action=duplicate&amp;project_id=<?= $task['project_id'] ?>&amp;task_id=<?= $task['id'] ?>"><?= t('Duplicate') ?></a></li>
            <li><a href="?controller=task&amp;action=copy&amp;project_id=<?= $task['project_id'] ?>&amp;task_id=<?= $task['id'] ?>"><?= t('Duplicate to another project') ?></a></li>
            <li><a href="?controller=task&amp;action=move&amp;project_id=<?= $task['project_id'] ?>&amp;task_id=<?= $task['id'] ?>"><?= t('Move to another project') ?></a></li>
            <li>
                <?php if ($task['is_active'] == 1): ?>
                    <a href="?controller=task&amp;action=close&amp;task_id=<?= $task['id'] ?>"><?= t('Close this task') ?></a>
                <?php else: ?>
                    <a href="?controller=task&amp;action=open&amp;task_id=<?= $task['id'] ?>"><?= t('Open this task') ?></a>
                <?php endif ?>
            </li>
            <?php if (! $hide_remove_menu): ?>
                <li><a href="?controller=task&amp;action=remove&amp;task_id=<?= $task['id'] ?>"><?= t('Remove') ?></a></li>
            <?php endif ?>
        </ul>
    </div>
</div>