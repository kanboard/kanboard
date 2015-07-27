<div class="page-header">
    <h2><?= t('Move the task to another project') ?></h2>
</div>

<?php if (empty($projects_list)): ?>
    <p class="alert"><?= t('There is no destination project available.') ?></p>
<?php else: ?>

    <form method="post" action="<?= $this->url->href('taskduplication', 'move', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">

        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('id', $values) ?>

        <?= $this->form->label(t('Project'), 'project_id') ?>
        <?= $this->form->select(
            'project_id',
            $projects_list,
            $values,
            array(),
            array('data-redirect="'.$this->url->href('taskduplication', 'move', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'dst_project_id' => 'PROJECT_ID')).'"'),
            'task-reload-project-destination'
        ) ?>
        <span class="loading-icon" style="display: none">&nbsp;<i class="fa fa-spinner fa-spin"></i></span>

        <?= $this->form->label(t('Swimlane'), 'swimlane_id') ?>
        <?= $this->form->select('swimlane_id', $swimlanes_list, $values) ?>
        <p class="form-help"><?= t('Current swimlane: %s', $task['swimlane_name'] ?: e($task['default_swimlane'])) ?></p>

        <?= $this->form->label(t('Column'), 'column_id') ?>
        <?= $this->form->select('column_id', $columns_list, $values) ?>
        <p class="form-help"><?= t('Current column: %s', $task['column_title']) ?></p>

        <?= $this->form->label(t('Category'), 'category_id') ?>
        <?= $this->form->select('category_id', $categories_list, $values) ?>
        <p class="form-help"><?= t('Current category: %s', $task['category_name'] ?: e('no category')) ?></p>

        <?= $this->form->label(t('Assignee'), 'owner_id') ?>
        <?= $this->form->select('owner_id', $users_list, $values) ?>
        <p class="form-help"><?= t('Current assignee: %s', ($task['assignee_name'] ?: $task['assignee_username']) ?: e('not assigned')) ?></p>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </div>
    </form>

<?php endif ?>