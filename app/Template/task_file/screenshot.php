<div class="page-header">
    <h2><?= t('Add a screenshot') ?></h2>
</div>

<div id="screenshot-zone">
    <p id="screenshot-inner"><?= t('Take a screenshot and press CTRL+V or ⌘+V to paste here.') ?></p>
</div>

<form action="<?= $this->url->href('TaskFileController', 'screenshot', array('task_id' => $task['id'])) ?>" method="post">
    <?= $this->form->csrf() ?>
    <?= $this->app->component('screenshot') ?>
    <?= $this->modal->submitButtons() ?>
</form>

<p class="alert alert-info"><?= t('This feature does not work with all browsers.') ?></p>
