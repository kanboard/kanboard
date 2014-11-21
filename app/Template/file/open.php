<div class="page-header">
    <h2><?= Helper\escape($file['name']) ?></h2>
    <div class="task-file-viewer">
        <img src="<?= Helper\u('file', 'image', array('file_id' => $file['id'], 'task_id' => $file['task_id'])) ?>" alt="<?= Helper\escape($file['name']) ?>"/>
    </div>
</div>