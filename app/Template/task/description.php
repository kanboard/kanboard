<section class="accordion-section <?= empty($task['description']) ? 'accordion-collapsed' : '' ?>">
    <div class="accordion-title">
        <h3><a href="#" class="fa accordion-toggle"></a> <?= t('Description') ?></h3>
    </div>
    <div class="accordion-content">
        <article class="markdown">
            <?php if (! isset($is_public)): ?>
                <?= $this->text->markdown(
                    $task['description'],
                    array(
                        'controller' => 'task',
                        'action' => 'show',
                        'params' => array(
                            'project_id' => $task['project_id']
                        )
                    )
                ) ?>
            <?php else: ?>
                <?= $this->text->markdown(
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
</section>