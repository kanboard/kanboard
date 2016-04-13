<div class="page-header">
    <h2><?= t('New task') ?></h2>
</div>

<form class="popover-form" method="post" action="<?= $this->url->href('taskcreation', 'save', array('project_id' => $values['project_id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <div class="form-column">
        <?= $this->form->label(t('Title'), 'title') ?>
        <?= $this->form->text('title', $values, $errors, array('autofocus', 'required', 'maxlength="200"', 'tabindex="1"'), 'form-input-large') ?>

        <?= $this->form->label(t('Description'), 'description') ?>
        <?= $this->form->textarea(
            'description',
            $values,
            $errors,
            array(
                'placeholder="'.t('Leave a description').'"',
                'tabindex="2"',
                'data-mention-search-url="'.$this->url->href('UserHelper', 'mention', array('project_id' => $values['project_id'])).'"'
            ),
            'markdown-editor'
        ) ?>

        <?= $this->render('task/color_picker', array('colors_list' => $colors_list, 'values' => $values)) ?>

        <?php if (! isset($duplicate)): ?>
            <?= $this->form->checkbox('another_task', t('Create another task'), 1, isset($values['another_task']) && $values['another_task'] == 1) ?>
        <?php endif ?>

        <?= $this->hook->render('template:task:form:left-column', array('values'=>$values, 'errors'=>$errors)) ?>
    </div>

    <div class="form-column">
        <?= $this->form->hidden('project_id', $values) ?>
        <?= $this->task->selectAssignee($users_list, $values, $errors) ?>
        <?= $this->task->selectCategory($categories_list, $values, $errors) ?>
        <?= $this->task->selectSwimlane($swimlanes_list, $values, $errors) ?>
        <?= $this->task->selectColumn($columns_list, $values, $errors) ?>
        <?= $this->task->selectPriority($project, $values) ?>
        <?= $this->task->selectScore($values, $errors) ?>
        <?= $this->task->selectTimeEstimated($values, $errors) ?>
        <?= $this->task->selectDueDate($values, $errors) ?>

        <?= $this->hook->render('template:task:form:right-column', array('values'=>$values, 'errors'=>$errors)) ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue" tabindex="15"><?= t('Save') ?></button>
        <?= t('or') ?> <?= $this->url->link(t('cancel'), 'board', 'show', array('project_id' => $values['project_id']), false, 'close-popover') ?>
    </div>
</form>
