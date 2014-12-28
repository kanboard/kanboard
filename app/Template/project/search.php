<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-table fa-fw"></i><?= $this->a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
            <li><i class="fa fa-check-square-o fa-fw"></i><?= $this->a(t('Completed tasks'), 'project', 'tasks', array('project_id' => $project['id'])) ?></li>
            <li><i class="fa fa-dashboard fa-fw"></i><?= $this->a(t('Activity'), 'project', 'activity', array('project_id' => $project['id'])) ?></li>
        </ul>
    </div>
    <section>
    <form method="get" action="?" autocomplete="off">
        <?= $this->formHidden('controller', $values) ?>
        <?= $this->formHidden('action', $values) ?>
        <?= $this->formHidden('project_id', $values) ?>
        <?= $this->formText('search', $values, array(), array('autofocus', 'required', 'placeholder="'.t('Search').'"'), 'form-input-large') ?>
        <input type="submit" value="<?= t('Search') ?>" class="btn btn-blue"/>
    </form>

    <?php if (empty($tasks) && ! empty($values['search'])): ?>
        <p class="alert"><?= t('Nothing found.') ?></p>
    <?php elseif (! empty($tasks)): ?>
        <?= $this->render('task/table', array(
            'tasks' => $tasks,
            'categories' => $categories,
            'columns' => $columns,
            'pagination' => $pagination,
        )) ?>
    <?php endif ?>

    </section>
</section>