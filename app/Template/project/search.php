<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-table fa-fw"></i><?= Helper\a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
            <li><i class="fa fa-check-square-o fa-fw"></i><?= Helper\a(t('Completed tasks'), 'project', 'tasks', array('project_id' => $project['id'])) ?></li>
            <li><i class="fa fa-dashboard fa-fw"></i><?= Helper\a(t('Activity'), 'project', 'activity', array('project_id' => $project['id'])) ?></li>
        </ul>
    </div>
    <section>
    <form method="get" action="?" autocomplete="off">
        <?= Helper\form_hidden('controller', $values) ?>
        <?= Helper\form_hidden('action', $values) ?>
        <?= Helper\form_hidden('project_id', $values) ?>
        <?= Helper\form_text('search', $values, array(), array('autofocus', 'required', 'placeholder="'.t('Search').'"'), 'form-input-large') ?>
        <input type="submit" value="<?= t('Search') ?>" class="btn btn-blue"/>
    </form>

    <?php if (empty($tasks) && ! empty($values['search'])): ?>
        <p class="alert"><?= t('Nothing found.') ?></p>
    <?php elseif (! empty($tasks)): ?>
        <?= Helper\template('task/table', array(
            'tasks' => $tasks,
            'categories' => $categories,
            'columns' => $columns,
            'pagination' => $pagination,
        )) ?>
    <?php endif ?>

    </section>
</section>