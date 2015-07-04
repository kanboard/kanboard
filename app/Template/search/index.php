<section id="main">
    <div class="page-header">
        <ul>
            <li>
                <i class="fa fa-folder fa-fw"></i>
                <?= $this->url->link(t('All projects'), 'project', 'index') ?>
            </li>
        </ul>
    </div>

    <form method="get" action="?" autocomplete="off">
        <?= $this->form->hidden('controller', $values) ?>
        <?= $this->form->hidden('action', $values) ?>
        <?= $this->form->text('search', $values, array(), array(empty($values['search']) ? 'autofocus' : '', 'required', 'placeholder="'.t('Search').'"'), 'form-input-large') ?>
        <input type="submit" value="<?= t('Search') ?>" class="btn btn-blue"/>
    </form>

    <?php if (empty($values['search'])): ?>
        <div class="listing">
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
            <p><a href="http://kanboard.net/documentation/search" target="_blank"><?= t('More examples in the documentation') ?></a></p>
        </div>
    <?php elseif (! empty($values['search']) && $paginator->isEmpty()): ?>
        <p class="alert"><?= t('Nothing found.') ?></p>
    <?php elseif (! $paginator->isEmpty()): ?>
        <?= $this->render('search/results', array(
            'paginator' => $paginator,
        )) ?>
    <?php endif ?>

</section>