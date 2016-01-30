<div class="page-header">
    <h2><?= t('Add a new link') ?></h2>
</div>

<form action="<?= $this->url->href('tasklink', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'redirect' => $redirect)) ?>" method="post" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', array('task_id' => $task['id'])) ?>
    <?= $this->form->hidden('opposite_task_id', $values) ?>

    <?= $this->form->label(t('Label'), 'link_id') ?>
    <?= $this->form->select('link_id', $labels, $values, $errors) ?>

    <?= $this->form->label(t('Task'), 'title') ?>
    <?= $this->form->text(
        'title',
        $values,
        $errors,
        array(
            'required',
            'autofocus',
            'placeholder="'.t('Start to type task title...').'"',
            'title="'.t('Start to type task title...').'"',
            'data-dst-field="opposite_task_id"',
            'data-search-url="'.$this->url->href('TaskHelper', 'autocomplete', array('exclude_task_id' => $task['id'])).'"',
        ),
        'autocomplete') ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->url->cancel($task, $redirect, 'links') ?>
    </div>
</form>