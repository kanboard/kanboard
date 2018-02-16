<div class="page-header">
    <h2><?= t('Attach a document') ?></h2>
</div>

<?= $this->app->component('file-upload', array(
    'csrf'              => $this->app->getToken()->getReusableCSRFToken(),
    'maxSize'           => $max_size,
    'url'               => $this->url->to('ProjectFileController', 'save', array('project_id' => $project['id'])),
    'labelDropzone'     => t('Drag and drop your files here'),
    'labelOr'           => t('or'),
    'labelChooseFiles'  => t('choose files'),
    'labelOversize'     => t('The maximum allowed file size is %sB.', $this->text->bytes($max_size)),
    'labelSuccess'      => t('All files have been uploaded successfully.'),
    'labelCloseSuccess' => t('Close this window'),
    'labelUploadError'  => t('Unable to upload this file.'),
)) ?>

<?= $this->modal->submitButtons(array(
    'submitLabel' => t('Upload files'),
    'disabled'    => true,
)) ?>
