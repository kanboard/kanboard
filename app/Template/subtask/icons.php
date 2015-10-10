<?php if ($subtask['status'] == 0): ?>
    <i class="fa fa-square-o fa-fw"></i>
<?php elseif ($subtask['status'] == 1): ?>
    <i class="fa fa-gears fa-fw"></i>
<?php else: ?>
    <i class="fa fa-check-square-o fa-fw"></i>
<?php endif ?>