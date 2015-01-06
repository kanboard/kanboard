<div class="page-header">
    <h2><?= $this->e($file['name']) ?></h2>
    <div class="task-file-viewer">
        <img src="<?= $this->u('file', 'image', array('file_id' => $file['id'], 'project_id' => $task['project_id'], 'task_id' => $file['task_id'])) ?>" alt="<?= $this->e($file['name']) ?>"/>
    </div>
</div>