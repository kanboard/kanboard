<div class="tooltip-large">
    <?php foreach ($comments as $comment): ?>
        <?= $this->render('comment/show', array(
            'comment' => $comment,
            'task' => $task,
            'hide_actions' => true,
        )) ?>
    <?php endforeach ?>
</div>
