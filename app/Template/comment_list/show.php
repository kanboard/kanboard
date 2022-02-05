<div class="page-header">
    <h2><?= $this->text->e($task['title']) ?></h2>
    <?php if (!isset($is_public) || !$is_public): ?>
        <ul>
            <li>
                <?= $this->url->icon('sort', t('Change sorting'), 'CommentListController', 'toggleSorting', array('task_id' => $task['id']), false, 'js-modal-replace') ?>
            </li>
            <?php if ($editable): ?>
                <li>
                    <?= $this->modal->medium('paper-plane', t('Send by email'), 'CommentMailController', 'create', array('task_id' => $task['id'])) ?>
                </li>
            <?php endif ?>
        </ul>
    <?php endif ?>
</div>
<div class="comments">
    <?php foreach ($comments as $comment): ?>
        <?= $this->render('comment/show', array(
            'comment'   => $comment,
            'task'      => $task,
            'editable'  => $editable,
            'is_public' => isset($is_public) && $is_public,
        )) ?>
    <?php endforeach ?>

    <?php if ($editable): ?>
        <?= $this->render('comment_list/create', array(
            'task' => $task,
        )) ?>
    <?php endif ?>
</div>
