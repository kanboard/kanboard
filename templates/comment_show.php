<div class="<?= isset($display_edit_form) && $display_edit_form === true ? 'comment-edit' : 'comment' ?>" id="comment-<?= $comment['id'] ?>">
    <p class="comment-title">
        <span class="comment-username"><?= Helper\escape($comment['username']) ?></span> @ <span class="comment-date"><?= dt('%B %e, %G at %k:%M %p', $comment['date']) ?></span>
    </p>
    <?php if (isset($task)): ?>
    <ul class="comment-actions">
        <li><a href="#comment-<?= $comment['id'] ?>"><?= t('link') ?></a></li>
        <?php if (Helper\is_admin() || Helper\is_current_user($comment['user_id'])): ?>
            <li>
                <a href="?controller=comment&amp;action=confirm&amp;project_id=<?= $task['project_id'] ?>&amp;comment_id=<?= $comment['id'] ?>"><?= t('remove') ?></a>
            </li>
            <li>
                <a href="?controller=comment&amp;action=edit&amp;task_id=<?= $task['id'] ?>&amp;comment_id=<?= $comment['id'] ?>#comment-<?= $comment['id'] ?>"><?= t('edit') ?></a>
            </li>
        <?php endif ?>
    </ul>
    <?php endif ?>

    <?php if (isset($display_edit_form) && $display_edit_form === true): ?>
        <form method="post" action="?controller=comment&amp;action=update&amp;task_id=<?= $task['id'] ?>&amp;comment_id=<?= $comment['id'] ?>" autocomplete="off">

            <?= Helper\form_hidden('id', $values) ?>
            <?= Helper\form_textarea('comment', $values, $errors, array('required', 'placeholder="'.t('Leave a comment').'"')) ?><br/>

            <div class="form-actions">
                <input type="submit" value="<?= t('Update this comment') ?>" class="btn btn-blue"/>
                <?= t('or') ?>
                <a href="?controller=task&amp;action=show&amp;task_id=<?= $task['id'] ?>"><?= t('cancel') ?></a>
            </div>
        </form>
    <?php else: ?>
    <div class="markdown">
        <?= Helper\markdown($comment['comment']) ?>
    </div>
    <?php endif ?>
</div>