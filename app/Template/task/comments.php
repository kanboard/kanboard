<?php if (! empty($comments)): ?>
<div id="comments" class="task-show-section">
    <div class="page-header">
        <h2><?= t('Comments') ?></h2>
    </div>

    <?php foreach ($comments as $comment): ?>
        <?= $this->render('comment/show', array(
            'comment' => $comment,
            'task' => $task,
            'project' => $project,
            'not_editable' => isset($not_editable) && $not_editable,
            'is_public' => isset($is_public) && $is_public,
        )) ?>
    <?php endforeach ?>

    <?php if (! isset($not_editable)): ?>
        <?= $this->render('comment/create', array(
                'skip_cancel' => true,
                'values' => array(
                    'user_id' => $this->userSession->getId(),
                    'task_id' => $task['id'],
                ),
                'errors' => array(),
                'task' => $task
        )) ?>
    <?php endif ?>
</div>
<?php endif ?>