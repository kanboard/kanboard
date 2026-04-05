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

<?php
/*
 * INLINE FLASH NOTIFICATION
 *
 * When a comment is added, edited, or removed via the modal AJAX system,
 * CommentController bypasses the PHP session flash (which only renders on
 * a full page load via layout.php) and passes the message directly here.
 *
 * The notification uses the same "alert" CSS classes the rest of Kanboard
 * uses, so it looks identical to the standard flash messages.
 *
 * The "kb-inline-flash" id is picked up by modal.js which fades the element
 * out after 3 seconds so the comment list is left clean.
 *
 * On non-AJAX renders (e.g. the task detail page) $notification_type is
 * never set so this block is skipped entirely — no behaviour change there.
 */
?>
<?php if (!empty($notification_type) && !empty($notification_message)): ?>
<div id="kb-inline-flash"
     class="alert alert-<?= $notification_type === 'success' ? 'success' : 'error' ?>">
    <?= $this->text->e($notification_message) ?>
</div>
<?php endif ?>

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