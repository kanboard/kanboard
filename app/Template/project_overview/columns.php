<div class="project-overview-columns">
    <?php foreach ($project['columns'] as $column): ?>
        <div class="project-overview-column">
            <strong title="<?= t('Task count') ?>"><?= $column['nb_tasks'] ?></strong><br>
            <span><?= $this->text->e($column['title']) ?></span>
        </div>
    <?php endforeach ?>
</div>
