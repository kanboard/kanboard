<div class="page-header">
    <h2><?= $this->text->e($project['name']) ?> &gt; <?= $this->text->e($task['title']) ?></h2>
</div>
<form method="post" action="<?= $this->url->href('TaskModificationController', 'update', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <div class="task-form-container">
        <div class="task-form-main-column">
            <?= $this->task->selectTitle($values, $errors) ?>
            <?= $this->task->selectDescription($values, $errors) ?>
            <?= $this->task->selectTags($project, $tags) ?>

            <?= $this->hook->render('template:task:form:first-column', array('values' => $values, 'errors' => $errors)) ?>
        </div>

        <div class="task-form-secondary-column">
            <?= $this->task->selectColor($values) ?>
            <?= $this->task->selectAssignee($users_list, $values, $errors) ?>
            <?= $this->task->selectCategory($categories_list, $values, $errors) ?>
            <?= $this->task->selectPriority($project, $values) ?>

            <?= $this->hook->render('template:task:form:second-column', array('values' => $values, 'errors' => $errors)) ?>
        </div>

        <div class="task-form-secondary-column">
            <?= $this->task->selectDueDate($values, $errors) ?>
            <?= $this->task->selectStartDate($values, $errors) ?>
            <?= $this->task->selectTimeEstimated($values, $errors) ?>
            <?= $this->task->selectTimeSpent($values, $errors) ?>
            <?= $this->task->selectScore($values, $errors) ?>
            <?= $this->task->selectReference($values, $errors) ?>

            <?= $this->hook->render('template:task:form:third-column', array('values' => $values, 'errors' => $errors)) ?>
        </div>

        <div class="task-form-bottom">
            <?= $this->modal->submitButtons() ?>
        </div>
    </div>
</form>
