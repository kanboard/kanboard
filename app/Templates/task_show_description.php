<?php if (! empty($task['description'])): ?>
    <div id="description" class="task-show-section">
        <div class="page-header">
            <h2><?= t('Description') ?></h2>
        </div>

        <article class="markdown task-show-description">
            <?= Helper\markdown($task['description']) ?: t('There is no description.') ?>
        </article>
    </div>
<?php endif ?>