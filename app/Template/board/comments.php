<section>
    <?php foreach ($comments as $comment): ?>
        <p class="comment-title">
            <?php if (! empty($comment['username'])): ?>
                <span class="comment-username"><?= $this->e($comment['name'] ?: $comment['username']) ?></span> @
            <?php endif ?>
            <span class="comment-date"><?= dt('%b %e, %Y, %k:%M %p', $comment['date_creation']) ?></span>
        </p>

		<div class="comment-inner">
            <div class="markdown">
                <?= $this->text->markdown($comment['comment']) ?>
            </div>
        </div>
    <?php endforeach ?>
</section>
