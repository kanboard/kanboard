<section>
    <table>
        <?php if (! empty($images)): ?>
            <?php foreach ($images as $file): ?>
                <tr>
                    <td class="column-70">
                        <i class="fa fa-file-image-o fa-fw"></i>
                        <?= $this->e($file['name']) ?>
                    </td>
                    <td>
                        <i class="fa fa-download"></i> <?= $this->a(t('download'), 'file', 'download', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                        <i class="fa fa-eye"></i> <?= $this->a(t('open'), 'file', 'open', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id']), false, 'popover') ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif ?>
        <?php if (! empty($files)): ?>
            <?php foreach ($files as $file): ?>
                <tr>
                    <td>
                        <i class="fa <?= $this->getFileIcon($file['name']) ?> fa-fw"></i>
                        <?= $this->e($file['name']) ?>
                    </td>
                    <td>
                        <i class="fa fa-download"></i> <?= $this->a(t('download'), 'file', 'download', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'file_id' => $file['id'])) ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif ?>
    </table>
</section>
