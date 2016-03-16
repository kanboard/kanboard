<div class="activity <?php echo $avatars ? : "no-avatars"; ?>">
    <?php if (empty($events)): ?>
        <p class="alert"><?= t('There is no activity yet.') ?></p>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
            <div class="activity-event">
                <div class="activity-content"><?= $event['event_content'] ?></div>
            </div>
        <?php endforeach ?>

    <?php endif ?>
</div>
