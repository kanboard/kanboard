<?php if (! empty($files)): ?>
<div id="attachments" class="task-show-section">

    <div class="page-header">
        <h2><?= t('Attachments') ?></h2>
    </div>

    <ul class="task-show-files">
    <?php foreach ($files as $file): ?>
        <li>
            <?= Helper\a(Helper\escape($file['name']), 'file', 'download', array('task_id' => $task['id'], 'file_id' => $file['id'])) ?>
            <span class="task-show-file-actions">
                <?php if ($file['is_image']): ?>
                    <?= Helper\a(t('open'), 'file', 'open', array('task_id' => $task['id'], 'file_id' => $file['id']), false, 'file-popover') ?>,
                <?php endif ?>
                <?= Helper\a(t('remove'), 'file', 'confirm', array('task_id' => $task['id'], 'file_id' => $file['id'])) ?>
            </span>
        </li>
    <?php endforeach ?>
    </ul>

</div>
<?php endif ?>