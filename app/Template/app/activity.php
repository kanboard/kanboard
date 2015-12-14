<div class="page-header">
    <h2><?= t('My activity stream') ?></h2>
</div>
<div class="search">
    <form method="get" action="<?= $this->url->dir() ?>" class="search">
        <?= $this->form->hidden('controller', array('controller' => 'search')) ?>
        <?= $this->form->hidden('action', array('action' => 'activity')) ?>
        <?= $this->form->text('search', array(), array(), array('placeholder="'.t('Search').'"'), 'form-input-large') ?>
    </form>

    <?= $this->render('app/activity_filters_helper') ?>
</div>
<?= $this->render('event/events', array('events' => $events)) ?>