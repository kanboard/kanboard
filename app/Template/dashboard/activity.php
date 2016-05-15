<div class="page-header">
    <h2><?= t('My activity stream') ?></h2>
</div>
<?= $this->render('event/events', array('events' => $events)) ?>