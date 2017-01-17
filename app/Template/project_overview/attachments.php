<section class="accordion-section <?= empty($files) && empty($images) ? 'accordion-collapsed' : '' ?>">
    <div class="accordion-title">
        <h3><a href="#" class="fa accordion-toggle"></a> <?= t('Attachments') ?></h3>
    </div>
    <div class="accordion-content">
        <?php if ($this->user->hasProjectAccess('ProjectFileController', 'create', $project['id'])): ?>
        <div class="buttons-header">
            <?= $this->modal->mediumButton('plus', t('Upload a file'), 'ProjectFileController', 'create', array('project_id' => $project['id'])) ?>
        </div>
        <?php endif ?>

        <?= $this->render('project_overview/images', array('project' => $project, 'images' => $images)) ?>
        <?= $this->render('project_overview/files', array('project' => $project, 'files' => $files)) ?>
    </div>
</section>
