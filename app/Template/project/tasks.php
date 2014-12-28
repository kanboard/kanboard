<section id="main">
    <div class="page-header">
        <ul>
            <li><i class="fa fa-table fa-fw"></i><?= $this->a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
            <li><i class="fa fa-search fa-fw"></i><?= $this->a(t('Search'), 'project', 'search', array('project_id' => $project['id'])) ?></li>
            <li><i class="fa fa-dashboard fa-fw"></i><?= $this->a(t('Activity'), 'project', 'activity', array('project_id' => $project['id'])) ?></li>
        </ul>
    </div>
    <section>
    <?php if (empty($tasks)): ?>
        <p class="alert"><?= t('No task') ?></p>
    <?php else: ?>
        <?= $this->render('task/table', array(
            'tasks' => $tasks,
            'categories' => $categories,
            'columns' => $columns,
            'pagination' => $pagination,
        )) ?>
    <?php endif ?>
    </section>
</section>