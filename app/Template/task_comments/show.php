<section class="accordion-section <?= empty($comments) ? 'accordion-collapsed' : '' ?>">
    <div class="accordion-title">
        <h3><a href="#" class="fa accordion-toggle"></a> <?= t('Comments') ?></h3>
    </div>
    <div class="accordion-content comments" id="comments">
        <?php if (!isset($is_public) || !$is_public): ?>
            <div class="comment-sorting">
                <small>
                    <?= $this->url->icon('sort', t('Change sorting'), 'CommentController', 'toggleSorting', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
                    <?php if ($editable): ?>
                        <?= $this->modal->medium('paper-plane', t('Send by email'), 'CommentMailController', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
                    <?php endif ?>
                </small>
            </div>
        <?php endif ?>
        <?php foreach ($comments as $comment): ?>
            <?= $this->render('comment/show', array(
                'comment'   => $comment,
                'task'      => $task,
                'project'   => $project,
                'editable'  => $editable,
                'is_public' => isset($is_public) && $is_public,
            )) ?>
        <?php endforeach ?>

        <?php if ($editable): ?>
            <?= $this->render('task_comments/create', array(
                'values'   => array(
                    'user_id' => $this->user->getId(),
                    'task_id' => $task['id'],
                    'project_id' => $task['project_id'],
                ),
                'errors'   => array(),
                'task'     => $task,
            )) ?>
        <?php endif ?>
    </div>
</section>
