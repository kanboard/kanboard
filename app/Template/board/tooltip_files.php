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
                <?= $this->url->icon('download', t('Download'), 'FileViewerController', 'download', array('task_id' => $task['id'], 'file_id' => $file['id'])) ?>
                <?php if ($this->file->getPreviewType($file['name']) !== null || $file['is_image'] == 1): ?>
                    &nbsp;<?= $this->modal->large('eye', t('View file'), 'FileViewerController', 'show', array('task_id' => $task['id'], 'file_id' => $file['id'])) ?>
                    &nbsp;<?= $this->url->icon('external-link', t('View file'), 'FileViewerController', ($file['is_image'] == 1 ? 'image' : 'show'), array('task_id' => $task['id'], 'file_id' => $file['id']), false, '', '', true) ?>
                <?php elseif ($this->file->getBrowserViewType($file['name']) !== null): ?>
                        <i class="fa fa-eye fa-fw"></i>
                        <?= $this->url->link(t('View file'), 'FileViewerController', 'browser', array('task_id' => $task['id'], 'file_id' => $file['id']), false, '', '', true) ?>
                <?php endif ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
</div>
