<div class="page-header">
    <h2><?= $this->text->e($task['title']) ?> &gt; <?= t('Comments') ?></h2>
    <?php if (!isset($is_public) || !$is_public): ?>
        <div class="comment-sorting">
            <small>
                <?= $this->url->icon('sort', t('change sorting'), 'CommentListController', 'toggleSorting', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'js-modal-replace') ?>
            </small>
        </div>
    <?php endif ?>
</div>
<div class="comments">
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
        <?= $this->render('comment_list/create', array(
            'task' => $task,
        )) ?>
    <?php endif ?>
</div>
