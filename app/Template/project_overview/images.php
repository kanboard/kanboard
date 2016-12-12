<?php if (! empty($images)): ?>
    <div class="file-thumbnails">
        <?php foreach ($images as $file): ?>
            <div class="file-thumbnail">
                <?= $this->app->component('image-slideshow', array(
                    'images' => $images,
                    'image' => $file,
                    'regex' => 'FILE_ID',
                    'url' => array(
                        'image' => $this->url->to('FileViewerController', 'image', array('file_id' => 'FILE_ID', 'project_id' => $project['id'])),
                        'thumbnail' => $this->url->to('FileViewerController', 'thumbnail', array('file_id' => 'FILE_ID', 'project_id' => $project['id'])),
                        'download' => $this->url->to('FileViewerController', 'download', array('file_id' => 'FILE_ID', 'project_id' => $project['id'])),
                    )
                )) ?>

                <div class="file-thumbnail-content">
                    <div class="file-thumbnail-title">
                        <div class="dropdown">
                            <a href="#" class="dropdown-menu dropdown-menu-link-text"><?= $this->text->e($file['name']) ?> <i class="fa fa-caret-down"></i></a>
                            <ul>
                                <li>
                                    <i class="fa fa-download fa-fw"></i>
                                    <?= $this->url->link(t('Download'), 'FileViewerController', 'download', array('project_id' => $project['id'], 'file_id' => $file['id'])) ?>
                                </li>
                                <?php if ($this->user->hasProjectAccess('ProjectFileController', 'remove', $project['id'])): ?>
                                    <li>
                                        <i class="fa fa-trash fa-fw"></i>
                                        <?= $this->url->link(t('Remove'), 'ProjectFileController', 'confirm', array('project_id' => $project['id'], 'file_id' => $file['id']), false, 'popover') ?>
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

