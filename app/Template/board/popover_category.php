<section id="main">
    <section>
        <h3><?= t('Change category for the task "%s"', $values['title']) ?></h3>
        <form method="post" action="<?= $this->url->href('board', 'updateCategory', array('task_id' => $values['id'], 'project_id' => $values['project_id'], 'redirect' => $redirect)) ?>">

            <?= $this->form->csrf() ?>

            <?= $this->form->hidden('id', $values) ?>
            <?= $this->form->hidden('project_id', $values) ?>

            <?= $this->form->label(t('Category'), 'category_id') ?>
            <?= $this->form->select('category_id', $categories_list, $values, array(), array('autofocus')) ?><br/>

            <div class="form-actions">
                <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
                <?= t('or') ?>
                <?php if (in_array($redirect, array('board', 'calendar', 'listing', 'roadmap'))): ?>
                    <?= $this->url->link(t('cancel'), $redirect, 'show', array('project_id' => $values['project_id'], false, 'close-popover')) ?>
                <?php else: ?>
                    <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => is_numeric($redirect) ? $redirect : $values['id'], 'project_id' => $values['project_id'])) ?>
                <?php endif ?>
            </div>
        </form>
    </section>
</section>