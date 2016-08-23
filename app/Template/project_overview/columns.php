<div class="project-overview-columns">
    <?php foreach ($project['columns'] as $column): ?>
        <div class="project-overview-column">
            <strong title="<?= t('Task count') ?>"><?= $column['nb_tasks'] ?></strong>
            <small><?= $this->text->e($column['title']) ?></small>
        </div>
    <?php endforeach ?>
</div>
