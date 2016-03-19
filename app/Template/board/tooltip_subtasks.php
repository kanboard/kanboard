<div class="tooltip-large">
    <table>
        <tr>
            <th class="column-80"><?= t('Subtask') ?></th>
            <th><?= t('Assignee') ?></th>
        </tr>
        <?php foreach ($subtasks as $subtask): ?>
        <tr>
            <td>
                <?= $this->subtask->toggleStatus($subtask, $task['project_id']) ?>
            </td>
            <td>
                <?php if (! empty($subtask['username'])): ?>
                    <?= $this->text->e($subtask['name'] ?: $subtask['username']) ?>
                <?php else: ?>
                    <?= t('Not assigned') ?>
                <?php endif ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
</div>
