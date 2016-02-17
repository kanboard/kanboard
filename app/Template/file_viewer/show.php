<div class="page-header">
    <h2><?= $this->e($file['name']) ?></h2>
    <div class="task-file-viewer">
        <?php if ($file['is_image']): ?>
            <img src="<?= $this->url->href('FileViewer', 'image', $params) ?>" alt="<?= $this->e($file['name']) ?>">
        <?php endif ?>
    </div>
</div>