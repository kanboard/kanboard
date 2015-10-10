<section id="main">
    <section>
        <h3><?= t('Change assignee for the task "%s"', $values['title']) ?></h3>
        <form method="post" action="<?= $this->url->href('board', 'updateAssignee', array('task_id' => $values['id'], 'project_id' => $values['project_id'])) ?>">

            <?= $this->form->csrf() ?>

            <?= $this->form->hidden('id', $values) ?>
            <?= $this->form->hidden('project_id', $values) ?>

            <?= $this->form->label(t('Assignee'), 'owner_id') ?>
            <?= $this->form->select('owner_id', $users_list, $values, array(), array('autofocus')) ?><br/>

            <div class="form-actions">
                <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
                <?= t('or') ?>
                <?= $this->url->link(t('cancel'), 'board', 'show', array('project_id' => $project['id']), false, 'close-popover') ?>
            </div>
        </form>
    </section>
</section>