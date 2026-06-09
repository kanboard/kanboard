<div class="page-header">
    <h2><?= t('Remove a comment') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this comment?') ?>
    </p>

    <?= $this->render('comment/show', array(
        'comment' => $comment,
        'task' => $task,
        'hide_actions' => true
    )) ?>

    <?php
    /*
     * WHY A FORM INSTEAD OF confirmButtons():
     *
     * The original confirmButtons() generated a plain <a href> GET link.
     * A plain anchor bypasses modal.js entirely — the browser navigates away,
     * the server redirect reloads the page, and the modal is destroyed.
     *
     * A <form method="post"> is intercepted by modal.js via KB.trigger('modal.submit'),
     * posted via XHR, and the HTML returned by CommentController::remove()
     * (the refreshed comment list) is fed into replace(html) — keeping the modal open.
     *
     * WHY getReusableCSRFToken() IN A HIDDEN FIELD:
     *
     * modal.js sends the form via XHR (KB.http.postForm → FormData). The POST body
     * is what the server receives. getStringParam() only reads the URL query string
     * (GET params), so a token in the query string would not survive a POST redirect
     * and is not accessible from POST body.
     *
     * CommentController::remove() is changed to call checkReusableCSRFParam() instead
     * of checkCSRFParam(). checkReusableCSRFParam() uses getRawValue() which reads
     * from the full request body — so it finds csrf_token in the POST body correctly.
     * getReusableCSRFToken() generates the matching reusable token for this validator.
     *
     * The "cancel" link uses js-modal-medium so clicking it reloads the comment
     * list inside the modal cleanly rather than silently closing it.
     */
    ?>
    <form method="post"
          action="<?= $this->url->href('CommentController', 'remove', array('task_id' => $task['id'], 'comment_id' => $comment['id'])) ?>"
          autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= $this->app->getToken()->getReusableCSRFToken() ?>">
        <div class="form-actions">
            <button type="submit" class="btn btn-red"><?= t('Yes, I confirm') ?></button>
            <span><?= t('or') ?></span>
            <a href="<?= $this->url->href('CommentListController', 'show', array('task_id' => $task['id'])) ?>"
               class="js-modal-medium"><?= t('cancel') ?></a>
        </div>
    </form>
</div>