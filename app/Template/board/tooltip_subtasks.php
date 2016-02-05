<table class="table-stripped">
<?php foreach ($subtasks as $subtask): ?>
    <tr>
        <td class="column-80">
            <?= $this->subtask->toggleStatus($subtask, $task['project_id']) ?>
        </td>
        <td>
            <?= $this->e($subtask['username'] ?: $this->user->getFullname($subtask)) ?>
        </td>
    </tr>
<?php endforeach ?>
</table>
