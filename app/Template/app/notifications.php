<div class="page-header">
    <h2><?= t('My notifications') ?></h2>

<?php if (empty($notifications)): ?>
    <p class="alert"><?= t('No new notifications.') ?></p>
</div>
<?php else: ?>
    <ul>
        <li>
            <i class="fa fa-check-square-o fa-fw"></i>
            <?= $this->url->link(t('Mark all as read'), 'webNotification', 'flush', array('user_id' => $user['id'])) ?>
        </li>
    </ul>
</div>

    <table class="table-fixed table-small">
        <tr>
            <th><?= t('Notification') ?></th>
            <th class="column-20"><?= t('Date') ?></th>
            <th class="column-15"><?= t('Action') ?></th>
        </tr>
        <?php foreach ($notifications as $notification): ?>
        <tr>
            <td>
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

                <?php if ($this->text->contains($notification['event_name'], 'comment')): ?>
                    <?= $this->url->link($notification['title'], 'task', 'show', array('task_id' => $notification['event_data']['task']['id'], 'project_id' => $notification['event_data']['task']['project_id']), false, '', '', false, 'comment-'.$notification['event_data']['comment']['id']) ?>
                <?php elseif ($this->text->contains($notification['event_name'], 'task.overdue')): ?>
                    <?php if (count($notification['event_data']['tasks']) > 1): ?>
                        <?= $notification['title'] ?>
                    <?php else: ?>
                        <?= $this->url->link($notification['title'], 'task', 'show', array('task_id' => $notification['event_data']['tasks'][0]['id'], 'project_id' => $notification['event_data']['tasks'][0]['project_id'])) ?>
                    <?php endif ?>
                <?php else: ?>
                    <?= $this->url->link($notification['title'], 'task', 'show', array('task_id' => $notification['event_data']['task']['id'], 'project_id' => $notification['event_data']['task']['project_id'])) ?>
                <?php endif ?>
            </td>
            <td>
                <?= dt('%B %e, %Y at %k:%M %p', $notification['date_creation']) ?>
            </td>
            <td>
                <i class="fa fa-check fa-fw"></i>
                <?= $this->url->link(t('Mark as read'), 'webNotification', 'remove', array('user_id' => $user['id'], 'notification_id' => $notification['id'])) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>