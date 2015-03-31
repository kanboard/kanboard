<?php if (empty($events)): ?>
    <p class="alert"><?= t('No activity.') ?></p>
<?php else: ?>

    <?php foreach ($events as $event): ?>
        <div class="activity-event">
            <p class="activity-datetime">
                <?php if ($this->contains($event['event_name'], 'subtask')): ?>
                    <i class="fa fa-tasks"></i>
                <?php elseif ($this->contains($event['event_name'], 'task')): ?>
                    <i class="fa fa-newspaper-o"></i>
                <?php elseif ($this->contains($event['event_name'], 'comment')): ?>
                    <i class="fa fa-comments-o"></i>
                <?php endif ?>
                &nbsp;<?= dt('%B %e, %Y at %k:%M %p', $event['date_creation']) ?>
            </p>
            <div class="activity-content"><?= $event['event_content'] ?></div>
        </div>
    <?php endforeach ?>

<?php endif ?>