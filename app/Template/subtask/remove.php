<div class="page-header">
    <h2><?= t('Remove a sub-task') ?></h2>
</div>

<div class="confirm">
    <div class="alert alert-info">
        <?= t('Do you really want to remove this sub-task?') ?>
        <ul>
            <li>
                <strong><?= $this->text->e($subtask['title']) ?></strong>
            </li>
        </ul>
    </div>

    <?= $this->modal->confirmButtons(
        'SubtaskController',
        'remove',
        array('task_id' => $task['id'], 'subtask_id' => $subtask['id'])
    ) ?>
</div>
