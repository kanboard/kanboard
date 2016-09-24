<div class="tooltip-large">
    <table class="table-small">
        <?php foreach ($files as $file): ?>
        <tr>
            <th>
                <i class="fa <?= $this->file->icon($file['name']) ?> fa-fw"></i>
                <?= $this->text->e($file['name']) ?>
            </th>
        </tr>
        <tr>
            <td>
                <?= $this->url->link('<i class="fa fa-download fa-fw"></i>' . t('download'), 'FileViewerController', 'download', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                <?php if ($file['is_image'] == 1): ?>
                    &nbsp;<?= $this->url->link('<i class="fa fa-eye"></i>' . t('open file'), 'FileViewerController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id']), false, 'popover') ?>
                <?php endif ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
</div>
