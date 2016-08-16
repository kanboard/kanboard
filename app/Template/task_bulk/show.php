<div class="page-header">
    <h2><?= $this->text->e($project['name']) ?> &gt; <?= t('Create tasks in bulk') ?></h2>
</div>

<form class="popover-form" method="post" action="<?= $this->url->href('TaskBulkController', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('column_id', $values) ?>
    <?= $this->form->hidden('swimlane_id', $values) ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->task->selectColor($values) ?>
    <?= $this->task->selectAssignee($users_list, $values, $errors) ?>
    <?= $this->task->selectCategory($categories_list, $values, $errors) ?>

    <?= $this->form->label(t('Tasks'), 'tasks') ?>
    <?= $this->form->textarea('tasks', $values, $errors, array('placeholder="'.t('My task title').'"')) ?>
    <p class="form-help"><?= t('Enter one task by line.') ?></p>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <?= t('or') ?> <?= $this->url->link(t('cancel'), 'BoardViewController', 'show', array('project_id' => $project['id']), false, 'close-popover') ?>
    </div>
</form>

