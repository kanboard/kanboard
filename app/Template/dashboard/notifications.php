<div class="page-header">
    <h2><?= t('My notifications') ?></h2>

<?php if (empty($notifications)): ?>
</div>
<p class="alert"><?= t('No new notifications.') ?></p>
<?php else: ?>
    <ul>
        <li>
            <i class="fa fa-check-square-o fa-fw"></i>
            <?= $this->url->link(t('Mark all as read'), 'WebNotificationController', 'flush', array('user_id' => $user['id'])) ?>
        </li>
    </ul>
</div>

    <table class="table-striped table-scrolling table-small">
        <tr>
            <th class="column-20"><?= t('Project') ?></th>
            <th><?= t('Notification') ?></th>
            <th class="column-15"><?= t('Date') ?></th>
            <th class="column-15"><?= t('Action') ?></th>
        </tr>
        <?php foreach ($notifications as $notification): ?>
        <tr>
            <td>
                <?php if (isset($notification['event_data']['task']['project_name'])): ?>
                    <?= $this->url->link(
                            $this->text->e($notification['event_data']['task']['project_name']),
                            'BoardViewController',
                            'show',
                            array('project_id' => $notification['event_data']['task']['project_id'])
                        )
                    ?>
                <?php elseif (isset($notification['event_data']['project_name'])): ?>
                    <?= $this->text->e($notification['event_data']['project_name']) ?>
                <?php endif ?>
            </td>
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

                <?php if ($this->text->contains($notification['event_name'], 'task.overdue') && count($notification['event_data']['tasks']) > 1): ?>
                    <?= $notification['title'] ?>
                <?php else: ?>
                    <?= $this->url->link($notification['title'], 'WebNotificationController', 'redirect', array('notification_id' => $notification['id'], 'user_id' => $user['id'])) ?>
                <?php endif ?>
            </td>
            <td>
                <?= $this->dt->datetime($notification['date_creation']) ?>
            </td>
            <td>
                <i class="fa fa-check fa-fw"></i>
                <?= $this->url->link(t('Mark as read'), 'WebNotificationController', 'remove', array('user_id' => $user['id'], 'notification_id' => $notification['id'])) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
