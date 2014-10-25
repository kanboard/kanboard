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
                    <a href="?controller=file&amp;action=open&amp;file_id=<?= $file['id'] ?>&amp;task_id=<?= $task['id'] ?>" class="file-popover"><?= t('open') ?></a>,
                <?php endif ?>
                <a href="?controller=file&amp;action=confirm&amp;file_id=<?= $file['id'] ?>&amp;task_id=<?= $task['id'] ?>"><?= t('remove') ?></a>
            </span>
        </li>
    <?php endforeach ?>
    </ul>

</div>
<?php endif ?>