<section id="main">

    <div class="page-header">
        <h2>
            <?= t('Dashboard'); ?>
        </h2>
    </div>
    <section id="dashboard">
        <h3>
            <a class="toggle-section" data-target="projects-div" href="#"><?= t('My projects') ?></a> |
            <a class="toggle-section" data-target="related-activity-div" href="#"><?= t('Recent activity on my projects') ?></a> |
            <a class="toggle-section" data-target="assigned-tasks-div" href="#"><?= t('My recent assigned tasks') ?></a> |
            <a class="toggle-section" data-target="assigned-activity-div" href="#"><?= t('Recent activity on my assigned tasks') ?></a>
        </h3>
        <div id="projects-div" class="dashboard-block">
        <ul class="project-listing">
        <?php foreach($board_selector as $board_id => $board_name): ?>
            <li><a href="?controller=board&action=show&project_id=<?= $board_id ?>"><?= Helper\escape($board_name) ?></a></li>
        <?php endforeach ?>
        </ul>
        </div>
        <div id="related-activity-div" style="display:none" class="dashboard-block">
        <?php foreach ($related_activity as $event): ?>
        <div class="activity-event">
            <p class="activity-datetime">
                <?php if ($event['event_type'] === 'task'): ?>
                    <i class="fa fa-newspaper-o"></i>
                <?php elseif ($event['event_type'] === 'subtask'): ?>
                    <i class="fa fa-tasks"></i>
                <?php elseif ($event['event_type'] === 'comment'): ?>
                    <i class="fa fa-comments-o"></i>
                <?php endif ?>
                &nbsp;<?= dt('%B %e, %Y at %k:%M %p', $event['date_creation']) ?>
                &nbsp;<?= t('Project') ?>: <a href="?controller=board&action=show&project_id=<?= $event['project_id'] ?>"><?= $event['project_name'] ?></a>
            </p>
            <div class="activity-content"><?= $event['event_content'] ?></div>
        </div>
        <?php endforeach ?>
        
        </div>
        <div id="assigned-tasks-div" style="display:none" class="dashboard-block">
        <ul class="task-listing">
        <?php foreach ($assigned_tasks as $task): ?>
            <li><a href="?controller=task&action=show&task_id=<?= $task['id'] ?>">#<?= $task['id'] ?></a> <?= $task['title'] ?><br />
                <ul class="task-listing-description">
                    <li>(<?= $task['column_title'] ?>) - 
                    <a href="?controller=board&action=show&project_id=<?= $task['project_id'] ?>"><?= Helper\escape($task['project_name']) ?></a></li>
                </ul>
            </li>
        <?php endforeach ?>
        </ul>
        </div>
        <div id="assigned-activity-div" style="display:none" class="dashboard-block">
        <?php foreach ($assigned_activity as $event): ?>
        <div class="activity-event">
            <p class="activity-datetime">
                <?php if ($event['event_type'] === 'task'): ?>
                    <i class="fa fa-newspaper-o"></i>
                <?php elseif ($event['event_type'] === 'subtask'): ?>
                    <i class="fa fa-tasks"></i>
                <?php elseif ($event['event_type'] === 'comment'): ?>
                    <i class="fa fa-comments-o"></i>
                <?php endif ?>
                &nbsp;<?= dt('%B %e, %Y at %k:%M %p', $event['date_creation']) ?>
                &nbsp;<?= t('Project') ?>: <a href="?controller=board&action=show&project_id=<?= $event['project_id'] ?>"><?= $event['project_name'] ?></a>
            </p>
            <div class="activity-content"><?= $event['event_content'] ?></div>
        </div>
        <?php endforeach ?>
        </div>
    </section>
</section>