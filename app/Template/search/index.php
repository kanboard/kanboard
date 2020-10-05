<div class="page-header">
    <ul>
        <li>
            <?= $this->url->icon('search', t('Activity stream search'), 'SearchController', 'activity') ?>
        </li>
    </ul>
</div>

<div class="filter-box margin-bottom">
    <form method="get" action="<?= $this->url->dir() ?>" class="search">
        <?= $this->form->hidden('controller', $values) ?>
        <?= $this->form->hidden('action', $values) ?>

        <div class="input-addon">
            <?= $this->form->text('search', $values, array(), array(empty($values['search']) ? 'autofocus' : '', 'placeholder="'.t('Search').'"', 'aria-label="'.t('Search').'"'), 'input-addon-field') ?>
            <div class="input-addon-item">
                <?= $this->render('app/filters_helper') ?>
            </div>
        </div>
    </form>
</div>

<?php if (empty($values['search'])): ?>
    <div class="panel">
        <h3><?= t('Advanced search') ?></h3>
        <p><?= t('Example of query: ') ?><strong>project:"My project" assignee:me due:tomorrow</strong></p>
        <ul>
            <li><?= t('Search by project: ') ?><strong>project:"My project"</strong></li>
            <li><?= t('Search by column: ') ?><strong>column:"Work in progress"</strong></li>
            <li><?= t('Search by assignee: ') ?><strong>assignee:nobody</strong></li>
            <li><?= t('Search by color: ') ?><strong>color:Blue</strong></li>
            <li><?= t('Search by category: ') ?><strong>category:"Feature Request"</strong></li>
            <li><?= t('Search by description: ') ?><strong>description:"Something to find"</strong></li>
            <li><?= t('Search by due date: ') ?><strong>due:2015-07-01</strong></li>
        </ul>
        <p><i class="fa fa-external-link fa-fw"></i><?= $this->url->doc(t('View advanced search syntax'), 'search') ?></p>
    </div>
<?php elseif (! empty($values['search']) && $paginator->isEmpty()): ?>
    <p class="alert"><?= t('Nothing found.') ?></p>
<?php elseif (! $paginator->isEmpty()): ?>
    <?= $this->render('search/results', array(
        'paginator' => $paginator,
    )) ?>
<?php endif ?>
