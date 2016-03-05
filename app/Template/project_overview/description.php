<?php if (! empty($project['description'])): ?>
    <div class="page-header">
        <h2><?= $this->text->e($project['name']) ?></h2>
    </div>
    <article class="markdown">
        <?= $this->text->markdown($project['description']) ?>
    </article>
<?php endif ?>
