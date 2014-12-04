<?php if (! empty($task['description'])): ?>
    <div id="description" class="task-show-section">
        <div class="page-header">
            <h2><?= t('Description') ?></h2>
        </div>

        <article class="markdown task-show-description">
            <?php if (! isset($is_public)): ?>
                <?= Helper\markdown($task['description']) ?>
            <?php else: ?>
                <?= Helper\markdown(
                    $task['description'],
                    array(
                        'controller' => 'task',
                        'action' => 'readonly',
                        'params' => array(
                            'token' => $project['token']
                        )
                    )
                ) ?>
            <?php endif ?>
        </article>
    </div>
<?php endif ?>