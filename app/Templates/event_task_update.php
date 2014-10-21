<?php if($gravatar_image): ?>
    <img class="activity-user-image" src="<?= $gravatar_image?>" alt="<?=Helper\escape($author)?>">
<?php endif;?>
<p class="activity-title">
    <?= e('%s updated the task <a href="?controller=task&amp;action=show&amp;task_id=%d">#%d</a>', Helper\escape($author), $task_id, $task_id) ?>
</p>
<p class="activity-description">
    <em><?= Helper\escape($task['title']) ?></em>
</p>