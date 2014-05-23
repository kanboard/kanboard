<div class="task-<?= $task['color_id'] ?> task-show-details">
    <h2><?= Helper\escape($task['title']) ?></h2>
    <?php if ($task['score']): ?>
        <span class="task-score"><?= Helper\escape($task['score']) ?></span>
    <?php endif ?>
    <ul>
        <li>
            <?= dt('Created on %B %e, %G at %k:%M %p', $task['date_creation']) ?>
        </li>
        <?php if ($task['date_completed']): ?>
        <li>
            <?= dt('Completed on %B %e, %G at %k:%M %p', $task['date_completed']) ?>
        </li>
        <?php endif ?>
        <?php if ($task['date_due']): ?>
        <li>
            <strong><?= dt('Must be done before %B %e, %G', $task['date_due']) ?></strong>
        </li>
        <?php endif ?>
        <li>
            <strong>
            <?php if ($task['username']): ?>
                <?= t('Assigned to %s', $task['username']) ?>
            <?php else: ?>
                <?= t('There is nobody assigned') ?>
            <?php endif ?>
            </strong>
        </li>
        <li>
            <?= t('Column on the board:') ?>
            <strong><?= Helper\escape($task['column_title']) ?></strong>
            (<?= Helper\escape($task['project_name']) ?>)
        </li>
        <?php if ($task['category_name']): ?>
        <li>
            <?= t('Category:') ?> <strong><?= Helper\escape($task['category_name']) ?></strong>
        </li>
        <?php endif ?>
        <li>
            <?php if ($task['is_active'] == 1): ?>
                <?= t('Status is open') ?>
            <?php else: ?>
                <?= t('Status is closed') ?>
            <?php endif ?>
        </li>
    </ul>
</div>


<?php if (! empty($task['description'])): ?>
<div id="description" class="task-show-section">
    <div class="page-header">
        <h2><?= t('Description') ?></h2>
    </div>

    <article class="markdown task-show-description">
        <?= Helper\parse($task['description']) ?: t('There is no description.') ?>
    </article>
</div>
<?php endif ?>


<?php if (! empty($files)): ?>
<div id="attachments" class="task-show-section">
    <div class="page-header">
        <h2><?= t('Attachments') ?></h2>
    </div>

    <ul class="task-show-files">
    <?php foreach ($files as $file): ?>
        <li>
            <a href="?controller=file&amp;action=download&amp;file_id=<?= $file['id'] ?>&amp;task_id=<?= $task['id'] ?>"><?= Helper\escape($file['name']) ?></a>
            <span class="task-show-file-actions">
                <?php if ($file['is_image']): ?>
                    <a href="?controller=file&amp;action=open&amp;file_id=<?= $file['id'] ?>&amp;task_id=<?= $task['id'] ?>" class="popover"><?= t('open') ?></a>,
                <?php endif ?>
                <a href="?controller=file&amp;action=confirm&amp;file_id=<?= $file['id'] ?>&amp;task_id=<?= $task['id'] ?>"><?= t('remove') ?></a>
            </span>
        </li>
    <?php endforeach ?>
    </ul>
</div>
<?php endif ?>


<?php if (! empty($comments)): ?>
<div id="comments" class="task-show-section">
    <div class="page-header">
        <h2><?= t('Comments') ?></h2>
    </div>

    <?php foreach ($comments as $comment): ?>
        <?= Helper\template('comment_show', array(
            'comment' => $comment,
            'task' => $task,
        )) ?>
    <?php endforeach ?>
</div>
<?php endif ?>
