<div class="page-header">
    <h2><?= t('Activity stream') ?></h2>
</div>

<?= $this->render('event/events', array('events' => $events)) ?>