<section class="accordion-section <?= empty($project['description']) ? 'accordion-collapsed' : '' ?>">
    <div class="accordion-title">
        <h3><a href="#" class="fa accordion-toggle"></a> <?= t('Description') ?></h3>
    </div>
    <div class="accordion-content">
        <article class="markdown">
            <?= $this->text->markdown($project['description']) ?>
        </article>
    </div>
</section>
