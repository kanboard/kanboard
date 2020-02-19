<div class="page-header">
    <h2><?= t('Swimlane modification for the project "%s"', $project['name']) ?></h2>
</div>

<form method="post" action="<?= $this->url->href('SwimlaneController', 'update', array('project_id' => $project['id'], 'swimlane_id' => $values['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="191"', 'tabindex="1"')) ?>

    <?= $this->form->label(t('Description'), 'description') ?>
    <?= $this->form->textEditor('description', $values, $errors, array('tabindex' => 2)) ?>

    <?= $this->form->label(t('Task limit'), 'task_limit') ?>
    <?= $this->form->number('task_limit', $values, $errors, array('tabindex' => 3)) ?>

    <?= $this->modal->submitButtons() ?>
</form>
