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
        <?= $this->form->text('search', $values, array(), array('autofocus', 'required', 'placeholder="'.t('Search').'"'), 'form-input-large') ?>
        <input type="submit" value="<?= t('Search') ?>" class="btn btn-blue"/>
    </form>

    <?php if (! empty($values['search']) && $paginator->isEmpty()): ?>
        <p class="alert"><?= t('Nothing found.') ?></p>
    <?php elseif (! $paginator->isEmpty()): ?>
        <?= $this->render('search/results', array(
            'paginator' => $paginator,
        )) ?>
    <?php endif ?>

</section>