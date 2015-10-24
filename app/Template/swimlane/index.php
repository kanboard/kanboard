<div class="page-header">
    <h2><?= t('Change default swimlane') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('swimlane', 'change', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('id', $default_swimlane) ?>

    <?= $this->form->label(t('Rename'), 'default_swimlane') ?>
    <?= $this->form->text('default_swimlane', $default_swimlane, array(), array('required', 'maxlength="50"')) ?><br/>

    <?php if (! empty($active_swimlanes) || $default_swimlane['show_default_swimlane'] == 0): ?>
        <?= $this->form->checkbox('show_default_swimlane', t('Show default swimlane'), 1, $default_swimlane['show_default_swimlane'] == 1) ?>
    <?php else: ?>
        <?= $this->form->hidden('show_default_swimlane', $default_swimlane) ?>
    <?php endif ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>

<?php if (! empty($active_swimlanes)): ?>
<div class="page-header">
    <h2><?= t('Active swimlanes') ?></h2>
</div>
<?= $this->render('swimlane/table', array('swimlanes' => $active_swimlanes, 'project' => $project)) ?>
<?php endif ?>

<?php if (! empty($inactive_swimlanes)): ?>
<div class="page-header">
    <h2><?= t('Inactive swimlanes') ?></h2>
</div>
<?= $this->render('swimlane/table', array('swimlanes' => $inactive_swimlanes, 'project' => $project, 'hide_position' => true)) ?>
<?php endif ?>

<div class="page-header">
    <h2><?= t('Add a new swimlane') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('swimlane', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('required', 'maxlength="50"')) ?>

    <?= $this->form->label(t('Description'), 'description') ?>

    <div class="form-tabs">
        <div class="write-area">
          <?= $this->form->textarea('description', $values, $errors) ?>
        </div>
        <div class="preview-area">
            <div class="markdown"></div>
        </div>
        <ul class="form-tabs-nav">
            <li class="form-tab form-tab-selected">
                <i class="fa fa-pencil-square-o fa-fw"></i><a id="markdown-write" href="#"><?= t('Write') ?></a>
            </li>
            <li class="form-tab">
                <a id="markdown-preview" href="#"><i class="fa fa-eye fa-fw"></i><?= t('Preview') ?></a>
            </li>
        </ul>
    </div>
    <div class="form-help"><?= $this->url->doc(t('Write your text in Markdown'), 'syntax-guide') ?></div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>
