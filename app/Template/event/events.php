<?php if ($events->isEmpty()): ?>
    <p class="alert"><?= t('There is no activity yet.') ?></p>
<?php else: ?>
    <?php foreach ($events->getCollection() as $event): ?>
        <div class="activity-event">
            <?= $this->avatar->render(
                $event['creator_id'],
                $event['author_username'],
                $event['author_name'],
                $event['email'],
                $event['avatar_path']
            ) ?>

            <div class="activity-content">
                <?= $event['event_content'] ?>
            </div>
        </div>
    <?php endforeach ?>
    <?= $events ?>
<?php endif ?>
