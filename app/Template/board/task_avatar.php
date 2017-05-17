    <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
        <span class="dropdown task-assignee task-change-assignee">
            <a href="#" class="dropdown-menu task-assignee" title="<?= t('Change assignee') ?>">
                <span class="task-avatar-assignee" title="<?= $this->text->e($task['assignee_name'] ?: $task['assignee_username']) ?>"><?= $this->text->e($task['assignee_name'] ?: $task['assignee_username']) ?></span>
                <?= $this->avatar->small(
                    $task['owner_id'],
                    $task['assignee_username'],
                    $task['assignee_name'],
                    $task['assignee_email'],
                    $task['assignee_avatar_path'],
                    'avatar-inline'
                ) ?>
                
            <?php if (! empty($users_list)) { ?>
                <span class="dropdown-menu-link-icon">
                    <i class="fa fa-caret-down"></i>
                </span>
            </a>
            <?php } // endif (! empty($users_list)) { ?>
            <ul>
                <li class="no-hover dropdown-filter-form" style="display:none;">
                    <form id="task-assignee-form-task-<?= $task['id'] ?>" method="post" action="<?= $this->url->href('TaskModificationController', 'update', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">
                        <?= $this->form->csrf() ?>
                        <?= $this->form->hidden('id', array('id' => $task['id'])) ?>
                        <?= $this->form->hidden('project_id', array('project_id' => $task['project_id'])) ?>
                        <?= $this->form->hidden('owner_id', array('owner_id' => $task['owner_id'])) ?>
                        <?= $this->form->hidden('title', array('title' => $this->text->e($task['title']))) ?>
                    </form>
                </li>
                <li class="no-hover dropdown-filter">
                    <input id="task-assignee-filter-task-<?= $task['id'] ?>" class="dropdown-filter-input" type="text" placeholder="<?= t('Type to filter...') ?>" />
                </li>
                <li>
                    <a href="#" class="dropdown-item task-assignee-dropdown-item" data-owner_id='0' data-task_id='<?= $this->text->e($task['id']) ?>'><?= t('Not assigned') ?></a>
                </li>
                <?php foreach ($users_list as $user_id => $user): ?>
                    <?php $users_list_avatars[$user_id] = $this->user->getUserDataById($user_id) ?>
                <?php endforeach ?>
                <?php foreach ($users_list_avatars as $user_id => $user): ?>
                    <li>
                        <?= $this->avatar->render($user['id'], $user['username'], $user['name'], $user['email'], $user['avatar_path'], 'avatar-inline', 20) ?>
                        <a href="#" class="task-assignee-dropdown-item" data-owner_id='<?= $this->text->e($user['id']) ?>' data-task_id='<?= $this->text->e($task['id']) ?>'><?= $this->text->e($user['name'] ?: $user['username']) ?></a>
                    </li> 
                <?php endforeach ?>
            </ul>
    </span>
    <?php else: ?>
    <span class="task-assignee">
        <span class="task-avatar-assignee"><?= $this->text->e($task['assignee_name'] ?: $task['assignee_username']) ?></span>
        <?= $this->avatar->small(
            $task['owner_id'],
            $task['assignee_username'],
            $task['assignee_name'],
            $task['assignee_email'],
            $task['assignee_avatar_path'],
            'avatar-inline'
        ) ?>
    </span>
    <?php endif ?>
