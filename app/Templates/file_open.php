<div class="page-header">
    <h2><?= Helper\escape($file['name']) ?></h2>
    <div class="task-file-viewer">
        <img src="?controller=file&amp;action=image&amp;file_id=<?= $file['id'] ?>&amp;task_id=<?= $file['task_id'] ?>" alt="<?= Helper\escape($file['name']) ?>"/>
    </div>
</div>