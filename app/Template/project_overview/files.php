<div class="page-header">
    <h2><?= t('Attachments') ?></h2>
    <?php if ($this->user->hasProjectAccess('ProjectFile', 'create', $project['id'])): ?>
    <ul>
        <li>
            <i class="fa fa-plus fa-fw"></i>
            <?= $this->url->link(t('Upload a file'), 'ProjectFile', 'create', array('project_id' => $project['id']), false, 'popover') ?>
        </li>
    </ul>
    <?php endif ?>
</div>

<?php if (empty($files) && empty($images)): ?>
    <p class="alert"><?= t('There is no attachment at the moment.') ?></p>
<?php endif ?>

<?php if (! empty($images)): ?>
<div class="file-thumbnails">
    <?php foreach ($images as $file): ?>
        <div class="file-thumbnail">
            <a href="<?= $this->url->href('FileViewer', 'show', array('project_id' => $project['id'], 'file_id' => $file['id'])) ?>" class="popover"><img src="<?= $this->url->href('FileViewer', 'thumbnail', array('file_id' => $file['id'], 'project_id' => $project['id'])) ?>" title="<?= $this->e($file['name']) ?>" alt="<?= $this->e($file['name']) ?>"></a>
            <div class="file-thumbnail-content">
                <div class="file-thumbnail-title">
                    <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-text"><?= $this->e($file['name']) ?> <i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li>
                            <i class="fa fa-download fa-fw"></i>
                            <?= $this->url->link(t('Download'), 'FileViewer', 'download', array('project_id' => $project['id'], 'file_id' => $file['id'])) ?>
                        </li>
                        <?php if ($this->user->hasProjectAccess('ProjectFile', 'remove', $project['id'])): ?>
                        <li>
                            <i class="fa fa-trash fa-fw"></i>
                            <?= $this->url->link(t('Remove'), 'ProjectFile', 'confirm', array('project_id' => $project['id'], 'file_id' => $file['id']), false, 'popover') ?>
                        </li>
                        <?php endif ?>
                    </ul>
                    </div>
                </div>
                <div class="file-thumbnail-description">
                    <span class="tooltip" title='<?= t('Uploaded: %s', $this->dt->datetime($file['date'])).'<br>'.t('Size: %s', $this->text->bytes($file['size'])) ?>'>
                        <i class="fa fa-info-circle"></i>
                    </span>
                    <?= t('Uploaded by %s', $file['user_name'] ?: $file['username']) ?>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>
<?php endif ?>

<?php if (! empty($files)): ?>
<table class="table-stripped">
    <tr>
        <th><?= t('Filename') ?></th>
        <th><?= t('Creator') ?></th>
        <th><?= t('Date') ?></th>
        <th><?= t('Size') ?></th>
    </tr>
    <?php foreach ($files as $file): ?>
        <tr>
            <td>
                <i class="fa <?= $this->file->icon($file['name']) ?> fa-fw"></i>
                <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-text"><?= $this->e($file['name']) ?> <i class="fa fa-caret-down"></i></a>
                    <ul>
                        <?php if ($this->file->getPreviewType($file['name']) !== null): ?>
                        <li>
                            <i class="fa fa-eye fa-fw"></i>
                            <?= $this->url->link(t('View file'), 'FileViewer', 'show', array('project_id' => $project['id'], 'file_id' => $file['id']), false, 'popover') ?>
                        </li>
                        <?php endif ?>
                        <li>
                            <i class="fa fa-download fa-fw"></i>
                            <?= $this->url->link(t('Download'), 'FileViewer', 'download', array('project_id' => $project['id'], 'file_id' => $file['id'])) ?>
                        </li>
                        <?php if ($this->user->hasProjectAccess('ProjectFile', 'remove', $project['id'])): ?>
                        <li>
                            <i class="fa fa-trash fa-fw"></i>
                            <?= $this->url->link(t('Remove'), 'ProjectFile', 'confirm', array('project_id' => $project['id'], 'file_id' => $file['id']), false, 'popover') ?>
                        </li>
                        <?php endif ?>
                    </ul>
                </div>
            </td>
            <td>
                <?= $this->e($file['user_name'] ?: $file['username']) ?>
            </td>
            <td>
                <?= $this->dt->date($file['date']) ?>
            </td>
            <td>
                <?= $this->text->bytes($file['size']) ?>
            </td>
        </tr>
    <?php endforeach ?>
</table>
<?php endif ?>
