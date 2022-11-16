<div class="page-header">
    <h2><?= t('Import Tasks') ?></h2>
</div>
<p><?= t('Please choose a project you want to copy the tasks from.') ?></p>
<?php if (count($projects) > 0) { ?>
    <form method="post" action="<?= $this->url->href('ProjectViewController', 'doTasksImport', ['project_id' => $project['id']]) ?>">
        <?= $this->form->csrf() ?>
        <select name="projects" id="projects">
            <option value="" disabled selected><?= t('Choose a project') ?></option>
            <?php foreach ($projects as $projectId => $projectName) { ?>
                <option value="<?= $projectId?>"><?= $projectName ?></option>
            <?php } ?>
        </select>
        <div class="form-actions">
            <button type="submit" class="btn btn-red"><?= t('Copy') ?></button>
            <?= t('or') ?> <?= $this->url->link(t('cancel'), 'ProjectViewController', 'show', array('project_id' => $project['id'])) ?>
        </div>
    </form>
<?php } else { ?>
    <p class="no-projects"><?= t('No other projects found.') ?></p>
<?php } ?>
