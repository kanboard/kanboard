<section class="accordion-section <?= empty($project['description']) ? 'accordion-collapsed' : '' ?>">
    <div class="accordion-title">
        <h3><a href="#" class="fa accordion-toggle"></a> <?= t('Description') ?></h3>
    </div>
    <div class="accordion-content">
        <?php if ($this->user->hasProjectAccess('ProjectEditController', 'description', $project['id'])): ?>
            <div class="buttons-header">
                <?= $this->url->button('fa-edit', t('Edit description'), 'ProjectEditController', 'description', array('project_id' => $project['id']), 'popover') ?>
            </div>
        <?php endif ?>
        <article class="markdown">
            <?= $this->text->markdown($project['description']) ?>
        </article>
    </div>
</section>
