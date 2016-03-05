<div class="page-header">
    <h2><?= t('Add a screenshot') ?></h2>
</div>

<div id="screenshot-zone">
    <p id="screenshot-inner"><?= t('Take a screenshot and press CTRL+V or ⌘+V to paste here.') ?></p>
</div>

<form class="popover-form" action="<?= $this->url->href('TaskFile', 'screenshot', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" method="post">
    <input type="hidden" name="screenshot"/>
    <?= $this->form->csrf() ?>
    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover') ?>
    </div>
</form>

<p class="alert alert-info"><?= t('This feature does not work with all browsers.') ?></p>