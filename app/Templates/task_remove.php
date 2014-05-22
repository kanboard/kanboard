<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this task: "%s"?', Helper\escape($task['title'])) ?>
    </p>

    <div class="form-actions">
        <a href="?controller=task&amp;action=remove&amp;task_id=<?= $task['id'] ?>" class="btn btn-red"><?= t('Yes') ?></a>
        <?= t('or') ?> <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>"><?= t('cancel') ?></a>
    </div>
</div>