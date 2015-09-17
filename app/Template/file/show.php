<?php if (! empty($files) || ! empty($images)): ?>
<div id="attachments" class="task-show-section">

    <div class="page-header">
        <h2><?= t('Attachments') ?></h2>
    </div>
    <?php if (! empty($images)): ?>
        <h3><?= t('Images') ?></h3>
        <ul class="task-show-images">
            <?php foreach ($images as $file): ?>
                <li>
                    <?php if (function_exists('imagecreatetruecolor')): ?>
                    <div class="img_container">
                        <img src="<?= $this->url->href('file', 'thumbnail', array('file_id' => $file['id'], 'project_id' => $task['project_id'], 'task_id' => $file['task_id'])) ?>" alt="<?= $this->e($file['name']) ?>"/>
                    </div>
                    <?php endif ?>
                    <p>
                        <?= $this->e($file['name']) ?>
                        <span class="tooltip" title='<?= t('uploaded by: %s', $file['user_name'] ?: $file['username']).'<br>'.t('uploaded on: %s', dt('%B %e, %Y at %k:%M %p', $file['date'])).'<br>'.t('size: %s', $this->text->bytes($file['size'])) ?>'>
                            <i class="fa fa-info-circle"></i>
                        </span>
                    </p>
                    <span class="task-show-file-actions task-show-image-actions">
                        <i class="fa fa-eye"></i> <?= $this->url->link(t('open file'), 'file', 'open', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id']), false, 'popover') ?>
                        <i class="fa fa-trash"></i> <?= $this->url->link(t('remove'), 'file', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                        <i class="fa fa-download"></i> <?= $this->url->link(t('download'), 'file', 'download', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                    </span>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>

    <?php if (! empty($files)): ?>
        <h3><?= t('Files') ?></h3>
        <table class="task-show-file-table">
            <?php foreach ($files as $file): ?>
                <tr>
                    <td><i class="fa <?= $this->file->icon($file['name']) ?> fa-fw"></i></td>
                    <td>
                        <?= $this->e($file['name']) ?>
                        <span class="tooltip" title='<?= t('uploaded by: %s', $file['user_name'] ?: $file['username']).'<br>'.t('uploaded on: %s', dt('%B %e, %Y at %k:%M %p', $file['date'])).'<br>'.t('size: %s', $this->text->bytes($file['size'])) ?>'>
                            <i class="fa fa-info-circle"></i>
                        </span>
                    </td>
                    <td>
                        <span class="task-show-file-actions">
                            <i class="fa fa-trash"></i> <?= $this->url->link(t('remove'), 'file', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                            <i class="fa fa-download"></i> <?= $this->url->link(t('download'), 'file', 'download', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>
</div>
<?php endif ?>