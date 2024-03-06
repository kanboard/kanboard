<div class="page-header">
    <h2><?= t('Add a new external link') ?></h2>
</div>

<form action="<?= $this->url->href('TaskExternalLinkController', 'create', array('task_id' => $task['id'])) ?>" method="post" autocomplete="off">
    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('External link'), 'text') ?>
    <?= $this->form->textarea(
        'text',
        $values,
        $errors,
        array(
            'required',
            'autofocus',
            'placeholder="'.t('Copy and paste your links here...').'"',
        )) ?>

    <?= $this->form->label(t('Link type'), 'type') ?>
    <?= $this->form->select('type', $types, $values) ?>

    <?= $this->modal->submitButtons() ?>
</form>
