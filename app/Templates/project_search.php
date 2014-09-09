<section id="main">
    <div class="page-header">
        <h2>
            <?= t('Search in the project "%s"', $project['name']) ?>
            <?php if (! empty($nb_tasks)): ?>
                <span id="page-counter"> (<?= $nb_tasks ?>)</span>
            <?php endif ?>
        </h2>
        <ul>
            <li><a href="?controller=board&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= t('Back to the board') ?></a></li>
            <li><a href="?controller=project&amp;action=tasks&amp;project_id=<?= $project['id'] ?>"><?= t('Completed tasks') ?></a></li>
            <li><a href="?controller=project&amp;action=activity&amp;project_id=<?= $project['id'] ?>"><?= t('Activity') ?></a></li>
            <li><a href="?controller=project&amp;action=index"><?= t('List of projects') ?></a></li>
        </ul>
    </div>
    <section>
    <form method="get" action="?" autocomplete="off">
        <?= Helper\form_hidden('controller', $values) ?>
        <?= Helper\form_hidden('action', $values) ?>
        <?= Helper\form_hidden('project_id', $values) ?>
        <?= Helper\form_text('search', $values, array(), array('autofocus', 'required', 'placeholder="'.t('Search').'"')) ?>
        <input type="submit" value="<?= t('Search') ?>" class="btn btn-blue"/>
    </form>

    <?php if (empty($tasks) && ! empty($values['search'])): ?>
        <p class="alert"><?= t('Nothing found.') ?></p>
    <?php elseif (! empty($tasks)): ?>
        <?= Helper\template('task_table', array('tasks' => $tasks, 'categories' => $categories, 'columns' => $columns)) ?>
    <?php endif ?>

    </section>
</section>