<div class="page-header">
    <h2><?= t('Add a screenshot') ?></h2>
</div>

<div id="screenshot-zone">
    <p id="screenshot-inner"><?= t('Take a screenshot and press CTRL+V or ⌘+V to paste here.') ?></p>
</div>

<form action="<?= $this->u('file', 'screenshot', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'redirect' => $redirect)) ?>" method="post">
    <input type="hidden" name="screenshot"/>
    <?= $this->formCsrf() ?>
    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover') ?>
    </div>
</form>
