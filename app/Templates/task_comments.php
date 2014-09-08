<?php if (! empty($comments)): ?>
<div id="comments" class="task-show-section">
    <div class="page-header">
        <h2><?= t('Comments') ?></h2>
    </div>

    <?php foreach ($comments as $comment): ?>
        <?= Helper\template('comment_show', array(
            'comment' => $comment,
            'task' => $task,
            'not_editable' => isset($not_editable) && $not_editable,
        )) ?>
    <?php endforeach ?>
</div>
<?php endif ?>