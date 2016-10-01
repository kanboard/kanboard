<section class="accordion-section <?= empty($events) ? 'accordion-collapsed' : '' ?>">
    <div class="accordion-title">
        <h3><a href="#" class="fa accordion-toggle"></a> <?= t('Last activity') ?></h3>
    </div>
    <div class="accordion-content">
        <?= $this->render('event/events', array('events' => $events)) ?>
    </div>
</section>