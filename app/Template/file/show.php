<?php if (!empty($files) || !empty($images)): ?>
    <div id="attachments" class="task-show-section">

        <div class="page-header">
            <h2><?= t('Attachments') ?></h2>
        </div>
        <?php if (!empty($images)): ?>
            <h3>
                <?= t('Images') ?>
            </h3>
            <ul class="task-show-images">
                <?php foreach ($images as $file): ?>
                    <li>
                        <div class="img_container">
                            <img src="<?= $this->u('file', 'image', array('file_id' => $file['id'], 'project_id' => $task['project_id'], 'task_id' => $file['task_id'])) ?>" alt="<?= $this->e($file['name']) ?>"/>
                        </div>
                        <p>
                            <?= $this->e($file['name']) ?>
                        </p>
                        <span class="task-show-file-actions task-show-image-actions">
                            <i class="fa fa-eye"></i> <?= $this->a(t('open'), 'file', 'open', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id']), false, 'popover') ?>
                            <i class="fa fa-trash"></i> <?= $this->a(t('remove'), 'file', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                            <i class="fa fa-download"></i> <?= $this->a(t('download'), 'file', 'download', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                        </span>
                    </li>                    
                <?php endforeach ?>
            </ul>            
        <?php endif
        ?>
        <?php if (!empty($files)): ?>
            <h3>
                <?= t('Files') ?>
            </h3>
            <ul class="task-show-files">
                <?php foreach ($files as $file): ?>
                    <li>
                        <?= $this->a($this->e($file['name']), 'file', 'download', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                        <span class="task-show-file-actions">                            
                            <i class="fa fa-trash"></i> <?= $this->a(t('remove'), 'file', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                        </span>
                    </li>
                <?php endforeach ?>
            </ul>

        </div>
    <?php endif
    ?>
    <?php




 endif ?>