<div class="page-header">
    <h2><?= t('Move selected tasks to another project') ?></h2>
</div>

<?php if (empty($projects_list)): ?>
    <p class="alert"><?= t('There is no destination project available.') ?></p>
    <div class="form-actions">
        <?= $this->url->link(t('cancel'), 'TaskListController', 'show', array('project_id' => $project['id']), false, 'js-modal-close btn') ?>
    </div>
<?php else: ?>

    <form method="post" action="<?= $this->url->href('TaskBulkMoveProjectController', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">

        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('task_ids', $values) ?>

        <?= $this->form->label(t('Project'), 'project_id') ?>
        <?= $this->app->component('select-dropdown-autocomplete', array(
            'name'         => 'project_id',
            'items'        => $projects_list,
            'defaultValue' => $dst_project_id,
            'placeholder'  => t('Choose a project'),
            'replace'      => array(
                'regex' => 'PROJECT_ID',
                'url' => $this->url->href('TaskBulkMoveProjectController', 'show', array('project_id' => $project['id'], 'task_ids' => $values['task_ids'], 'dst_project_id' => 'PROJECT_ID')),
            )
        )) ?>

        <?= $this->form->label(t('Swimlane'), 'swimlane_id') ?>
        <?= $this->form->select('swimlane_id', array('' => t('Keep current value')) + $swimlanes_list, $values, $errors) ?>

        <?= $this->form->label(t('Column'), 'column_id') ?>
        <?= $this->form->select('column_id', array('' => t('Keep current value')) + $columns_list, $values, $errors) ?>

        <?= $this->form->label(t('Category'), 'category_id') ?>
        <?= $this->form->select('category_id', array('' => t('Keep current value')) + $categories_list, $values, $errors) ?>

        <?= $this->form->label(t('Assignee'), 'owner_id') ?>
        <?= $this->form->select('owner_id', array('' => t('Keep current value')) + $users_list, $values, $errors) ?>

        <p class="form-help"><?= t('If not selected, the current swimlane, column, category and assignee of each task will be kept when they exist in the destination project.') ?></p>

        <?= $this->modal->submitButtons() ?>
    </form>

<?php endif ?>
