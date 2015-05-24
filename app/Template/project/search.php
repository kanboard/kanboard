<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <i class="fa fa-table fa-fw"></i>
                <?= $this->url->link(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <i class="fa fa-calendar fa-fw"></i>
                <?= $this->url->link(t('Calendar'), 'calendar', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <i class="fa fa-check-square-o fa-fw"></i>
                <?= $this->url->link(t('Completed tasks'), 'project', 'tasks', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <i class="fa fa-dashboard fa-fw"></i>
                <?= $this->url->link(t('Activity'), 'project', 'activity', array('project_id' => $project['id'])) ?>
            </li>
        </ul>
    </div>
    <section>
    <form method="get" action="?" autocomplete="off">
        <?= $this->form->hidden('controller', $values) ?>
        <?= $this->form->hidden('action', $values) ?>
        <?= $this->form->hidden('project_id', $values) ?>
        <?= $this->form->text('search', $values, array(), array('autofocus', 'required', 'placeholder="'.t('Search').'"'), 'form-input-large') ?>
        <input type="submit" value="<?= t('Search') ?>" class="btn btn-blue"/>
    </form>

    <?php if (! empty($values['search']) && $paginator->isEmpty()): ?>
        <p class="alert"><?= t('Nothing found.') ?></p>
    <?php elseif (! $paginator->isEmpty()): ?>
        <?= $this->render('task/table', array(
            'paginator' => $paginator,
            'categories' => $categories,
            'columns' => $columns,
        )) ?>
    <?php endif ?>

    </section>
</section>