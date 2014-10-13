<section id="main">
    <div class="page-header">
        <h2><?= t('%s\'s activity', $project['name']) ?></h2>
        <ul>
            <li><?= Helper\a(t('Back to the board'), 'board', 'show', array('project_id' => $project['id'])) ?></li>
            <li><?= Helper\a(t('Search'), 'project', 'search', array('project_id' => $project['id'])) ?></li>
            <li><?= Helper\a(t('Completed tasks'), 'project', 'tasks', array('project_id' => $project['id'])) ?></li>
            <li><?= Helper\a(t('List of projects'), 'project', 'index') ?></li>
        </ul>
    </div>
    <section>
    <?php if (empty($events)): ?>
        <p class="alert"><?= t('No activity.') ?></p>
    <?php else: ?>

        <?php if ($project['is_public']): ?>
            <p class="pull-right"><i class="fa fa-rss-square"></i> <?= Helper\a(t('RSS feed'), 'project', 'feed', array('token' => $project['token'])) ?></p>
        <?php endif ?>

        <?php foreach ($events as $event): ?>
        <div class="activity-event">
            <p class="activity-datetime">
                <?php if (Helper\contains($event['event_name'], 'task')): ?>
                    <i class="fa fa-newspaper-o"></i>
                <?php elseif (Helper\contains($event['event_name'], 'subtask')): ?>
                    <i class="fa fa-tasks"></i>
                <?php elseif (Helper\contains($event['event_name'], 'comment')): ?>
                    <i class="fa fa-comments-o"></i>
                <?php endif ?>
                &nbsp;<?= dt('%B %e, %Y at %k:%M %p', $event['date_creation']) ?>
            </p>
            <div class="activity-content"><?= $event['event_content'] ?></div>
        </div>
        <?php endforeach ?>
    <?php endif ?>
    </section>
</section>