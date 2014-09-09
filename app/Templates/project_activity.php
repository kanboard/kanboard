<section id="main">
    <div class="page-header">
        <h2><?= t('%s\'s activity', $project['name']) ?></h2>
        <ul>
            <li><a href="?controller=board&amp;action=show&amp;project_id=<?= $project['id'] ?>"><?= t('Back to the board') ?></a></li>
            <li><a href="?controller=project&amp;action=search&amp;project_id=<?= $project['id'] ?>"><?= t('Search') ?></a></li>
            <li><a href="?controller=project&amp;action=tasks&amp;project_id=<?= $project['id'] ?>"><?= t('Completed tasks') ?></a></li>
            <li><a href="?controller=project&amp;action=index"><?= t('List of projects') ?></a></li>
        </ul>
    </div>
    <section>
    <?php if (empty($events)): ?>
        <p class="alert"><?= t('No activity.') ?></p>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
        <div class="activity-event">
            <p class="activity-datetime">
                <?php if ($event['event_type'] === 'task'): ?>
                    <i class="fa fa-newspaper-o"></i>
                <?php endif ?>
                &nbsp;<?= dt('%B %e, %Y at %k:%M %p', $event['date_creation']) ?>
            </p>
            <div class="activity-content"><?= $event['event_content'] ?></div>
        </div>
        <?php endforeach ?>
    <?php endif ?>
    </section>
</section>