<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <i class="fa fa-folder fa-fw"></i>
                <?= $this->url->link(t('All projects'), 'project', 'index') ?>
            </li>
        </ul>
    </div>

    <div class="filter-box">
        <form method="get" action="<?= $this->url->dir() ?>" class="search">
            <?= $this->form->hidden('controller', $values) ?>
            <?= $this->form->hidden('action', $values) ?>
            <?= $this->form->text('search', $values, array(), array(empty($values['search']) ? 'autofocus' : '', 'placeholder="'.t('Search').'"'), 'form-input-large') ?>
            <?= $this->render('app/activity_filters_helper') ?>
        </form>
    </div>

    <?php if (empty($values['search'])): ?>
        <div class="listing">
            <h3><?= t('Advanced search') ?></h3>
            <p><?= t('Example of query: ') ?><strong>project:"My project" creator:me created:yesterday</strong></p>
            <ul>
                <li><?= t('Search by creator: ') ?><strong>creator:nobody|me|name|surname|username</strong></li>
                <li><?= t('Search by creation date: ') ?><strong>creation:(<=>)yesterday|today|2015-07-01</strong></li>
                <li><?= t('Search by status: ') ?><strong>status:closed|open</strong></li>
            </ul>
            <p><i class="fa fa-external-link fa-fw"></i><?= $this->url->doc(t('View advanced search syntax'), 'search') ?></p>
        </div>
    <?php elseif (! empty($values['search']) && $paginator->isEmpty()): ?>
        <p class="alert"><?= t('Nothing found.') ?></p>
    <?php elseif (! $paginator->isEmpty()): ?>
        <div class="page-header">
            <ul>
                <li>
                    <?= $paginator->order(t('Order by Date'), 'id') ?>
                </li>
                <li>
                    <?= $paginator->order(t('Order by Task'), 'task_id') ?>
                </li>
            </ul>
        </div>
        <?= $this->render('search/activity_results', array(
            'paginator' => $paginator,
        )) ?>
    <?php endif ?>

</section>