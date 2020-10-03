<div class="project-overview-columns">
    <?php foreach ($columns as $column): ?>
        <div class="project-overview-column">
            <strong title="<?= t('Task count') ?>"><span class="ui-helper-hidden-accessible"><?= t('Task count') ?> </span><?= $column['nb_open_tasks'] ?></strong>
            <small><?= $this->text->e($column['title']) ?></small>
        </div>
    <?php endforeach ?>
</div>
