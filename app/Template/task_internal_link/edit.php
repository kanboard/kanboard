<div class="page-header">
    <h2><?= t('Edit link') ?></h2>
</div>

<form action="<?= $this->url->href('TaskInternalLinkController', 'update', array('task_id' => $task['id'], 'link_id' => $task_link['id'])) ?>" method="post" autocomplete="off">
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

    <?= $this->modal->submitButtons() ?>
</form>
