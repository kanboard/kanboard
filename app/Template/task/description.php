<?php if (! empty($task['description'])): ?>
    <div id="description" class="task-show-section">
        <div class="page-header">
            <h2><?= t('Description') ?></h2>
            <ul>
                <li>
                    <i class="fa fa-edit fa-fw"></i>
                    <?= $this->url->link(t('Edit the description'), 'taskmodification', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
                </li>
            </ul>
        </div>

        <article class="markdown task-show-description">
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
<?php endif ?>