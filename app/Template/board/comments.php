<section>
    <?php foreach ($comments as $comment): ?>
        <p class="comment-title">
            <span class="comment-username"><?= $this->e($comment['name'] ?: $comment['username']) ?></span> @ <span class="comment-date"><?= dt('%b %e, %Y, %k:%M %p', $comment['date']) ?></span>
        </p>

		<div class="comment-inner">
            <div class="markdown">
                <?= $this->markdown($comment['comment']) ?>
            </div>
        </div>
    <?php endforeach ?>
</section>
