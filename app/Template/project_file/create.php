<div class="page-header">
    <h2><?= t('Attach a document') ?></h2>
</div>
<div id="file-done" style="display:none">
    <p class="alert alert-success">
        <?= t('All files have been uploaded successfully.') ?>
        <?= $this->url->link(t('View uploaded files'), 'ProjectOverviewController', 'show', array('project_id' => $project['id'])) ?>
    </p>
</div>

<div id="file-error-max-size" style="display:none">
    <p class="alert alert-error">
        <?= t('The maximum allowed file size is %sB.', $this->text->bytes($max_size)) ?>
        <a href="#" id="file-browser"><?= t('Choose files again') ?></a>
    </p>
</div>

<div
    id="file-dropzone"
    data-max-size="<?= $max_size ?>"
    data-url="<?= $this->url->href('ProjectFileController', 'save', array('project_id' => $project['id'])) ?>">
    <div id="file-dropzone-inner">
        <?= t('Drag and drop your files here') ?> <?= t('or') ?> <a href="#" id="file-browser"><?= t('choose files') ?></a>
    </div>
</div>

<input type="file" name="files[]" multiple style="display:none" id="file-form-element">

<div class="form-actions">
    <input type="submit" value="<?= t('Upload files') ?>" class="btn btn-blue" id="file-upload-button" disabled>
    <?= t('or') ?>
    <?= $this->url->link(t('cancel'), 'ProjectOverviewController', 'show', array('project_id' => $project['id']), false, 'close-popover') ?>
</div>
