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
                <?= $this->url->icon('download', t('Download'), 'FileViewerController', 'download', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                <?php if ($file['is_image'] == 1): ?>
                    &nbsp;<?= $this->modal->large('eye', t('View file'), 'FileViewerController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                    &nbsp;<?= $this->url->icon('external-link', t('View file'), 'FileViewerController', 'image', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id']), false, '', '', true) ?>
                <?php endif ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
</div>
