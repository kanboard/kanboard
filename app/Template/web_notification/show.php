<div class="page-header">
    <h2><?= t('My notifications') ?></h2>

    <?php if (! empty($notifications)): ?>
    <ul>
        <li>
            <?= $this->modal->replaceIconLink('check-square-o', t('Mark all as read'), 'WebNotificationController', 'flush', array('user_id' => $user['id'])) ?>
        </li>
    </ul>
    <?php endif ?>
</div>

<?php if (empty($notifications)): ?>
    <p class="alert"><?= t('No notification.') ?></p>
<?php else: ?>
<div class="table-list">
    <div class="table-list-header">
        <div class="table-list-header-count">
            <?php if ($nb_notifications > 1): ?>
                <?= t('%d notifications', $nb_notifications) ?>
            <?php else: ?>
                <?= t('%d notification', $nb_notifications) ?>
            <?php endif ?>
        </div>
        &nbsp;
    </div>
    <?php foreach ($notifications as $notification): ?>
    <div class="table-list-row table-border-left">
        <span class="table-list-title">
            <?php if ($this->text->contains($notification['event_name'], 'subtask')): ?>
                <i class="fa fa-tasks fa-fw"></i>
            <?php elseif ($this->text->contains($notification['event_name'], 'task.move')): ?>
                <i class="fa fa-arrows-alt fa-fw"></i>
            <?php elseif ($this->text->contains($notification['event_name'], 'task.overdue')): ?>
                <i class="fa fa-calendar-times-o fa-fw"></i>
            <?php elseif ($this->text->contains($notification['event_name'], 'task')): ?>
                <i class="fa fa-newspaper-o fa-fw"></i>
            <?php elseif ($this->text->contains($notification['event_name'], 'comment')): ?>
                <i class="fa fa-comments-o fa-fw"></i>
            <?php elseif ($this->text->contains($notification['event_name'], 'file')): ?>
                <i class="fa fa-file-o fa-fw"></i>
            <?php endif ?>

            <?php if (isset($notification['event_data']['task']['project_name'])): ?>
                <?= $this->url->link(
                    $this->text->e($notification['event_data']['task']['project_name']),
                    'BoardViewController',
                    'show',
                    array('project_id' => $notification['event_data']['task']['project_id'])
                ) ?> &gt;
            <?php elseif (isset($notification['event_data']['project_name'])): ?>
                <?= $this->text->e($notification['event_data']['project_name']) ?> &gt;
            <?php endif ?>

            <?php if ($this->text->contains($notification['event_name'], 'task.overdue') && count($notification['event_data']['tasks']) > 1): ?>
                <?= $notification['title'] ?>
            <?php else: ?>
                <?= $this->url->link($notification['title'], 'WebNotificationController', 'redirect', array('notification_id' => $notification['id'], 'user_id' => $user['id'])) ?>
            <?php endif ?>
        </span>
        <div class="table-list-details">
            <?= $this->dt->datetime($notification['date_creation']) ?>
            <?= $this->modal->replaceIconLink('check', t('Mark as read'), 'WebNotificationController', 'remove', array('user_id' => $user['id'], 'notification_id' => $notification['id'])) ?>
        </div>
    </div>
    <?php endforeach ?>
</div>
<?php endif ?>