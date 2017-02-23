<div class="page-header">
    <h2><?= t('Move the task to another project') ?></h2>
</div>

<?php if (empty($projects_list)): ?>
    <p class="alert"><?= t('There is no destination project available.') ?></p>
    <div class="form-actions">
        <?= $this->url->link(t('cancel'), 'TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'js-modal-close btn') ?>
    </div>
<?php else: ?>

    <form method="post" action="<?= $this->url->href('TaskDuplicationController', 'move', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">

        <?= $this->form->csrf() ?>
        <?= $this->form->hidden('id', $values) ?>

        <?= $this->form->label(t('Project'), 'project_id') ?>
        <?= $this->app->component('select-dropdown-autocomplete', array(
            'name'         => 'project_id',
            'items'        => $projects_list,
            'defaultValue' => isset($values['project_id']) ? $values['project_id'] : null,
            'placeholder'  => t('Choose a project'),
            'replace'      => array(
                'regex' => 'PROJECT_ID',
                'url' => $this->url->href('TaskDuplicationController', 'move', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'dst_project_id' => 'PROJECT_ID')),
            )
        )) ?>

        <?= $this->form->label(t('Swimlane'), 'swimlane_id') ?>
        <?= $this->form->select('swimlane_id', $swimlanes_list, $values) ?>
        <p class="form-help"><?= t('Current swimlane: %s', $task['swimlane_name']) ?></p>

        <?= $this->form->label(t('Column'), 'column_id') ?>
        <?= $this->form->select('column_id', $columns_list, $values) ?>
        <p class="form-help"><?= t('Current column: %s', $task['column_title']) ?></p>

        <?= $this->form->label(t('Category'), 'category_id') ?>
        <?= $this->form->select('category_id', $categories_list, $values) ?>
        <p class="form-help"><?= t('Current category: %s', $task['category_name'] ?: e('no category')) ?></p>

        <?= $this->form->label(t('Assignee'), 'owner_id') ?>
        <?= $this->form->select('owner_id', $users_list, $values) ?>
        <p class="form-help"><?= t('Current assignee: %s', ($task['assignee_name'] ?: $task['assignee_username']) ?: e('not assigned')) ?></p>

        <?= $this->modal->submitButtons() ?>
    </form>

<?php endif ?>
