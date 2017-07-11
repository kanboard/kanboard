<?= $this->hook->render('template:task:show:top', array('task' => $task, 'project' => $project)) ?>

<?= $this->render('task/details', array(
    'task' => $task,
    'tags' => $tags,
    'project' => $project,
    'editable' => $this->user->hasProjectAccess('TaskModificationController', 'edit', $project['id']),
)) ?>

<?php if (!empty($task['description'])): ?>
    <?= $this->hook->render('template:task:show:before-description', array('task' => $task, 'project' => $project)) ?>
    <?= $this->render('task/description', array('task' => $task)) ?>
<?php endif ?>

<?php if(!empty($subtasks)): ?>
    <?= $this->hook->render('template:task:show:before-subtasks', array('task' => $task, 'project' => $project)) ?>
    <?= $this->render('subtask/show', array(
        'task' => $task,
        'subtasks' => $subtasks,
        'project' => $project,
        'editable' => $this->user->hasProjectAccess('SubtaskController', 'edit', $project['id']),
    )) ?>
<?php endif ?>

<?php if (!empty($internal_links)): ?>
    <?= $this->hook->render('template:task:show:before-internal-links', array('task' => $task, 'project' => $project)) ?>
    <?= $this->render('task_internal_link/show', array(
        'task' => $task,
        'links' => $internal_links,
        'project' => $project,
        'link_label_list' => $link_label_list,
        'editable' => $this->user->hasProjectAccess('TaskInternalLinkController', 'edit', $project['id']),
        'is_public' => false,
    )) ?>
<?php endif ?>

<?php if (!empty($external_links)): ?>
    <?= $this->hook->render('template:task:show:before-external-links', array('task' => $task, 'project' => $project)) ?>
    <?= $this->render('task_external_link/show', array(
        'task' => $task,
        'links' => $external_links,
        'project' => $project,
    )) ?>
<?php endif ?>

<?php if (!empty($files) || !empty($images)): ?>
    <?= $this->hook->render('template:task:show:before-attachments', array('task' => $task, 'project' => $project)) ?>
    <?= $this->render('task_file/show', array(
        'task' => $task,
        'files' => $files,
        'images' => $images
    )) ?>
<?php endif ?>

<?php if (!empty($comments)): ?>
    <?= $this->hook->render('template:task:show:before-comments', array('task' => $task, 'project' => $project)) ?>
    <?= $this->render('task_comments/show', array(
        'task' => $task,
        'comments' => $comments,
        'project' => $project,
        'editable' => $this->user->hasProjectAccess('CommentController', 'edit', $project['id']),
    )) ?>
<?php endif ?>

<?= $this->hook->render('template:task:show:bottom', array('task' => $task, 'project' => $project)) ?>
