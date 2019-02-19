<details class="accordion-section" <?= empty($files) && empty($images) ? '' : 'open' ?>>
    <summary class="accordion-title"><?= t('Attachments') ?></summary>
    <div class="accordion-content">
        <?php if ($this->user->hasProjectAccess('ProjectFileController', 'create', $project['id'])): ?>
        <div class="buttons-header">
            <?= $this->modal->mediumButton('plus', t('Upload a file'), 'ProjectFileController', 'create', array('project_id' => $project['id'])) ?>
        </div>
        <?php endif ?>

        <?= $this->render('project_overview/images', array('project' => $project, 'images' => $images)) ?>
        <?= $this->render('project_overview/files', array('project' => $project, 'files' => $files)) ?>
    </div>
</details>
