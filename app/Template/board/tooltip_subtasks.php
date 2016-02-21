<table class="table-stripped">
<?php foreach ($subtasks as $subtask): ?>
    <tr>
        <td class="column-80">
            <?= $this->subtask->toggleStatus($subtask, $task['project_id']) ?>
        </td>
        <td>
            <?php if (! empty($subtask['username'])): ?>
                    <?= $this->e($subtask['name'] ?: $subtask['username']) ?>
            <?php endif ?>
        </td>
    </tr>
<?php endforeach ?>
</table>
