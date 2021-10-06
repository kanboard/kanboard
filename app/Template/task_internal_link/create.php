<div class="page-header">
    <h2><?= t('Add a new link') ?></h2>
</div>

<form action="<?= $this->url->href('TaskInternalLinkController', 'save', array('task_id' => $task['id'])) ?>" method="post" autocomplete="off">

    <?= $this->form->csrf() ?>
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
            'placeholder="'.t('Start to type task title...').'"',
            'title="'.t('Start to type task title...').'"',
            'data-dst-field="opposite_task_id"',
            'data-search-url="'.$this->url->href('TaskAjaxController', 'autocomplete', array('exclude_task_id' => $task['id'])).'"',
        ),
        'autocomplete') ?>

    <?= $this->form->checkbox('another_tasklink', t('Create another link'), 1, isset($values['another_tasklink']) && $values['another_tasklink'] == 1) ?>

    <?= $this->modal->submitButtons() ?>
</form>
