<?php

require __DIR__.'/app/common.php';

$models = array(
    'Config',
    'Project',
    'ProjectPermission',
    'Task',
    'TaskCreation',
    'TaskModification',
    'TaskFinder',
    'TaskPosition',
    'TaskStatus',
    'TaskValidator',
    'User',
    'Category',
    'Comment',
    'SubTask',
    'Board',
    'Action',
    'Webhook',
    'Notification',
);

$events = array(
    'actionModel',
    'projectModel',
    'webhookModel',
    'notificationModel',
);

foreach ($models as $model) {
    $variable = lcfirst($model).'Model';
    $class = '\Model\\'.$model;
    $$variable = new $class($container);
}

foreach ($events as $class) {
    $$class->attachEvents();
}

$configModel->setupTranslations();
$configModel->setupTimezone();

$server = new JsonRPC\Server;
$server->authentication(array('jsonrpc' => $configModel->get('api_token')));

/**
 * Project procedures
 */
$server->bind('getProjectById', $projectModel, 'getById');
$server->bind('getProjectByName', $projectModel, 'getByName');
$server->bind('getAllProjects', $projectModel, 'getAll');
$server->bind('removeProject', $projectModel, 'remove');
$server->bind('enableProject', $projectModel, 'enable');
$server->bind('disableProject', $projectModel, 'disable');
$server->bind('enableProjectPublicAccess', $projectModel, 'enablePublicAccess');
$server->bind('disableProjectPublicAccess', $projectModel, 'disablePublicAccess');

$server->register('createProject', function($name) use ($projectModel) {
    $values = array('name' => $name);
    list($valid,) = $projectModel->validateCreation($values);
    return $valid && $projectModel->create($values);
});

$server->register('updateProject', function($id, $name, $is_active = null, $is_public = null, $token = null) use ($projectModel) {

    $values = array(
        'id' => $id,
        'name' => $name,
        'is_active' => $is_active,
        'is_public' => $is_public,
        'token' => $token,
    );

    foreach ($values as $key => $value) {
        if (is_null($value)) {
            unset($values[$key]);
        }
    }

    list($valid,) = $projectModel->validateModification($values);
    return $valid && $projectModel->update($values);
});

/**
 * Board procedures
 */
$server->bind('getBoard', $boardModel, 'get');
$server->bind('getColumns', $boardModel, 'getColumns');
$server->bind('getColumn', $boardModel, 'getColumn');
$server->bind('moveColumnUp', $boardModel, 'moveUp');
$server->bind('moveColumnDown', $boardModel, 'moveDown');
$server->bind('updateColumn', $boardModel, 'updateColumn');
$server->bind('addColumn', $boardModel, 'addColumn');
$server->bind('removeColumn', $boardModel, 'removeColumn');

/**
 * Project permissions procedures
 */
$server->bind('getMembers', $projectPermissionModel, 'getMembers');
$server->bind('revokeUser', $projectPermissionModel, 'revokeUser');
$server->bind('allowUser', $projectPermissionModel, 'allowUser');

/**
 * Task procedures
 */
$server->bind('getTask', $taskFinderModel, 'getById');
$server->bind('getAllTasks', $taskFinderModel, 'getAll');
$server->bind('openTask', $taskStatusModel, 'open');
$server->bind('closeTask', $taskStatusModel, 'close');
$server->bind('removeTask', $taskModel, 'remove');
$server->bind('moveTaskPosition', $taskPositionModel, 'movePosition');

$server->register('createTask', function($title, $project_id, $color_id = '', $column_id = 0, $owner_id = 0, $creator_id = 0, $date_due = '', $description = '', $category_id = 0, $score = 0) use ($taskCreationModel, $taskValidatorModel) {

    $values = array(
        'title' => $title,
        'project_id' => $project_id,
        'color_id' => $color_id,
        'column_id' => $column_id,
        'owner_id' => $owner_id,
        'creator_id' => $creator_id,
        'date_due' => $date_due,
        'description' => $description,
        'category_id' => $category_id,
        'score' => $score,
    );

    list($valid,) = $taskValidatorModel->validateCreation($values);

    if (! $valid) {
        return false;
    }

    return $taskCreationModel->create($values);
});

$server->register('updateTask', function($id, $title = null, $project_id = null, $color_id = null, $column_id = null, $owner_id = null, $creator_id = null, $date_due = null, $description = null, $category_id = null, $score = null) use ($taskModificationModel, $taskValidatorModel) {

    $values = array(
        'id' => $id,
        'title' => $title,
        'project_id' => $project_id,
        'color_id' => $color_id,
        'column_id' => $column_id,
        'owner_id' => $owner_id,
        'creator_id' => $creator_id,
        'date_due' => $date_due,
        'description' => $description,
        'category_id' => $category_id,
        'score' => $score,
    );

    foreach ($values as $key => $value) {
        if (is_null($value)) {
            unset($values[$key]);
        }
    }

    list($valid) = $taskValidatorModel->validateApiModification($values);
    return $valid && $taskModificationModel->update($values);
});


/**
 * User procedures
 */
$server->bind('getUser', $userModel, 'getById');
$server->bind('getAllUsers', $userModel, 'getAll');
$server->bind('removeUser', $userModel, 'remove');

$server->register('createUser', function($username, $password, $name = '', $email = '', $is_admin = 0, $default_project_id = 0) use ($userModel) {

    $values = array(
        'username' => $username,
        'password' => $password,
        'confirmation' => $password,
        'name' => $name,
        'email' => $email,
        'is_admin' => $is_admin,
        'default_project_id' => $default_project_id,
    );

    list($valid,) = $userModel->validateCreation($values);

    if (! $valid) {
        return false;
    }

    return $userModel->create($values);
});

$server->register('updateUser', function($id, $username = null, $name = null, $email = null, $is_admin = null, $default_project_id = null) use ($userModel) {

    $values = array(
        'id' => $id,
        'username' => $username,
        'name' => $name,
        'email' => $email,
        'is_admin' => $is_admin,
        'default_project_id' => $default_project_id,
    );

    foreach ($values as $key => $value) {
        if (is_null($value)) {
            unset($values[$key]);
        }
    }

    list($valid,) = $userModel->validateApiModification($values);
    return $valid && $userModel->update($values);
});

/**
 * Category procedures
 */
$server->bind('getCategory', $categoryModel, 'getById');
$server->bind('getAllCategories', $categoryModel, 'getAll');
$server->bind('removeCategory', $categoryModel, 'remove');

$server->register('createCategory', function($project_id, $name) use ($categoryModel) {

    $values = array(
        'project_id' => $project_id,
        'name' => $name,
    );

    list($valid,) = $categoryModel->validateCreation($values);

    if (! $valid) {
        return false;
    }

    return $categoryModel->create($values);
});

$server->register('updateCategory', function($id, $name) use ($categoryModel) {

    $values = array(
        'id' => $id,
        'name' => $name,
    );

    list($valid,) = $categoryModel->validateModification($values);
    return $valid && $categoryModel->update($values);
});

/**
 * Comments procedures
 */
$server->bind('getComment', $commentModel, 'getById');
$server->bind('getAllComments', $commentModel, 'getAll');
$server->bind('removeComment', $commentModel, 'remove');

$server->register('createComment', function($task_id, $user_id, $content) use ($commentModel) {

    $values = array(
        'task_id' => $task_id,
        'user_id' => $user_id,
        'comment' => $content,
    );

    list($valid,) = $commentModel->validateCreation($values);

    if (! $valid) {
        return false;
    }

    return $commentModel->create($values);
});

$server->register('updateComment', function($id, $content) use ($commentModel) {

    $values = array(
        'id' => $id,
        'comment' => $content,
    );

    list($valid,) = $commentModel->validateModification($values);
    return $valid && $commentModel->update($values);
});

/**
 * Subtask procedures
 */
$server->bind('getSubtask', $subTaskModel, 'getById');
$server->bind('getAllSubtasks', $subTaskModel, 'getAll');
$server->bind('removeSubtask', $subTaskModel, 'remove');

$server->register('createSubtask', function($task_id, $title, $user_id = 0, $time_estimated = 0, $time_spent = 0, $status = 0) use ($subTaskModel) {

    $values = array(
        'title' => $title,
        'task_id' => $task_id,
        'user_id' => $user_id,
        'time_estimated' => $time_estimated,
        'time_spent' => $time_spent,
        'status' => $status,
    );

    foreach ($values as $key => $value) {
        if (is_null($value)) {
            unset($values[$key]);
        }
    }

    list($valid,) = $subTaskModel->validateCreation($values);

    if (! $valid) {
        return false;
    }

    return $subTaskModel->create($values);
});

$server->register('updateSubtask', function($id, $task_id, $title = null, $user_id = null, $time_estimated = null, $time_spent = null, $status = null) use ($subTaskModel) {

    $values = array(
        'id' => $id,
        'task_id' => $task_id,
        'title' => $title,
        'user_id' => $user_id,
        'time_estimated' => $time_estimated,
        'time_spent' => $time_spent,
        'status' => $status,
    );

    foreach ($values as $key => $value) {
        if (is_null($value)) {
            unset($values[$key]);
        }
    }

    list($valid,) = $subTaskModel->validateApiModification($values);
    return $valid && $subTaskModel->update($values);
});

/**
 * Application procedures
 */
$server->register('getTimezone', function() use($configModel) {
    return $configModel->get('application_timezone');
});

/**
 * Parse incoming requests
 */
echo $server->execute();
