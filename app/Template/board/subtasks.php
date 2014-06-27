<section>
<?php
function subTaskText($subtask) {
    $text = Helper\escape($subtask['title']);
    if (isset($subtask['username']))
        $text .= " [" . $subtask['username'] . "]";
    return $text;
}
?>
    <?php foreach ($subtasks as $subtask): ?>
        <a href="?controller=board&amp;action=toggleSubtask&amp;task_id=<?= $task['id'] ?>&amp;subtask_id=<?= $subtask['id'] ?>">
            <?php if ($subtask['status'] == 0): ?>
                <i class="fa fa-square-o fa-fw" title="<?= Helper\escape($subtask['status_name']) ?>">
            <?php elseif ($subtask['status'] == 1): ?>
                <i class="fa fa-gears fa-fw" title="<?= Helper\escape($subtask['status_name']) ?>">
            <?php else: ?>
                <i class="fa fa-check-square-o fa-fw" title="<?= Helper\escape($subtask['status_name']) ?>">
            <?php endif ?>
        </i></a>
        &nbsp;<?= subTaskText($subtask) ?><br>
    <?php endforeach ?>
</section>
