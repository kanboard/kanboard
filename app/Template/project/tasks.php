<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <i class="fa fa-table fa-fw"></i>
                <?= $this->a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <i class="fa fa-calendar fa-fw"></i>
                <?= $this->a(t('Calendar'), 'calendar', 'show', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <i class="fa fa-search fa-fw"></i>
                <?= $this->a(t('Search'), 'project', 'search', array('project_id' => $project['id'])) ?>
            </li>
            <li>
                <i class="fa fa-dashboard fa-fw"></i>
                <?= $this->a(t('Activity'), 'project', 'activity', array('project_id' => $project['id'])) ?>
            </li>
        </ul>
    </div>
    <section>
    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('No task') ?></p>
    <?php else: ?>
        <?= $this->render('task/table', array(
            'paginator' => $paginator,
            'categories' => $categories,
            'columns' => $columns,
        )) ?>
    <?php endif ?>
    </section>
</section>