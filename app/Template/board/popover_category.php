<section id="main">
    <div class="page-header">
        <h2><?= t('Change category for the task "%s"', $values['title']) ?></h2>
    </div>
    <form class="popover-form" method="post" action="<?= $this->url->href('BoardPopover', 'updateCategory', array('task_id' => $values['id'], 'project_id' => $project['id'])) ?>">

        <?= $this->form->csrf() ?>

        <?= $this->form->hidden('id', $values) ?>
        <?= $this->form->hidden('project_id', $values) ?>

        <?= $this->task->selectCategory($categories_list, $values, array(), array('autofocus'), true) ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'board', 'show', array('project_id' => $project['id']), false, 'close-popover') ?>
        </div>
    </form>
</section>