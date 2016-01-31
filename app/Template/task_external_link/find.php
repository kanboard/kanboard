<div class="page-header">
    <h2><?= t('Add a new external link') ?></h2>
</div>

<form class="popover-form" action="<?= $this->url->href('TaskExternalLink', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" method="post" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', array('task_id' => $task['id'])) ?>

    <?= $this->form->label(t('External link'), 'text') ?>
    <?= $this->form->text(
        'text',
        $values,
        $errors,
        array(
            'required',
            'autofocus',
            'placeholder="'.t('Copy and paste your link here...').'"',
        )) ?>

    <?= $this->form->label(t('Link type'), 'type') ?>
    <?= $this->form->select('type', $types, $values) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Next') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover') ?>
    </div>
</form>