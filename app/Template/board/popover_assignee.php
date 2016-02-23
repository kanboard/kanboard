<section id="main">
    <div class="page-header">
        <h2><?= t('Change assignee for the task "%s"', $values['title']) ?></h2>
    </div>
    <form method="post" action="<?= $this->url->href('BoardPopover', 'updateAssignee', array('task_id' => $values['id'], 'project_id' => $project['id'])) ?>">

        <?= $this->form->csrf() ?>

        <?= $this->form->hidden('id', $values) ?>
        <?= $this->form->hidden('project_id', $values) ?>

        <?= $this->task->selectAssignee($users_list, $values, array(), array('autofocus')) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'board', 'show', array('project_id' => $project['id']), false, 'close-popover') ?>
        </div>
    </form>
</section>