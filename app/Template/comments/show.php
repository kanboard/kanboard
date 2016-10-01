<section class="accordion-section <?= empty($comments) ? 'accordion-collapsed' : '' ?>">
    <div class="accordion-title">
        <h3><a href="#" class="fa accordion-toggle"></a> <?= t('Comments') ?></h3>
    </div>
    <div class="accordion-content" id="comments">
        <?php if (!isset($is_public) || !$is_public): ?>
            <div class="comment-sorting">
                <small>
                    <i class="fa fa-sort"></i>
                    <?= $this->url->link(t('change sorting'), 'CommentController', 'toggleSorting', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
                </small>
            </div>
        <?php endif ?>
        <?php foreach ($comments as $comment): ?>
            <?= $this->render('comment/show', array(
                'comment' => $comment,
                'task' => $task,
                'project' => $project,
                'editable' => $editable,
                'is_public' => isset($is_public) && $is_public,
            )) ?>
        <?php endforeach ?>

        <?php if ($editable): ?>
            <?= $this->render('comments/create', array(
                'values' => array(
                    'user_id' => $this->user->getId(),
                    'task_id' => $task['id'],
                ),
                'errors' => array(),
                'task' => $task,
            )) ?>
        <?php endif ?>
    </div>
</section>
