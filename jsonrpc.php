<?php

require __DIR__.'/app/common.php';

use Symfony\Component\EventDispatcher\Event;

$container['dispatcher']->dispatch('api.bootstrap', new Event);

$server = new JsonRPC\Server;
$server->authentication(array('jsonrpc' => $container['config']->get('api_token')));

/**
 * Project procedures
 */
$server->bind('getProjectById', $container['project'], 'getById');
$server->bind('getProjectByName', $container['project'], 'getByName');
$server->bind('getAllProjects', $container['project'], 'getAll');
$server->bind('removeProject', $container['project'], 'remove');
$server->bind('enableProject', $container['project'], 'enable');
$server->bind('disableProject', $container['project'], 'disable');
$server->bind('enableProjectPublicAccess', $container['project'], 'enablePublicAccess');
$server->bind('disableProjectPublicAccess', $container['project'], 'disablePublicAccess');

$server->register('createProject', function($name) use ($container) {
    $values = array('name' => $name);
    list($valid,) = $container['project']->validateCreation($values);
    return $valid ? $container['project']->create($values) : false;
});

$server->register('updateProject', function($id, $name, $is_active = null, $is_public = null, $token = null) use ($container) {

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

    list($valid,) = $container['project']->validateModification($values);
    return $valid && $container['project']->update($values);
});

/**
 * Board procedures
 */
$server->bind('getBoard', $container['board'], 'getBoard');
$server->bind('getColumns', $container['board'], 'getColumns');
$server->bind('getColumn', $container['board'], 'getColumn');
$server->bind('moveColumnUp', $container['board'], 'moveUp');
$server->bind('moveColumnDown', $container['board'], 'moveDown');
$server->bind('updateColumn', $container['board'], 'updateColumn');
$server->bind('addColumn', $container['board'], 'addColumn');
$server->bind('removeColumn', $container['board'], 'removeColumn');

/**
 * Project permissions procedures
 */
$server->bind('getMembers', $container['projectPermission'], 'getMembers');
$server->bind('revokeUser', $container['projectPermission'], 'revokeMember');
$server->bind('allowUser', $container['projectPermission'], 'addMember');

/**
 * Task procedures
 */
$server->bind('getTask', $container['taskFinder'], 'getById');
$server->bind('getAllTasks', $container['taskFinder'], 'getAll');
$server->bind('openTask', $container['taskStatus'], 'open');
$server->bind('closeTask', $container['taskStatus'], 'close');
$server->bind('removeTask', $container['task'], 'remove');
$server->bind('moveTaskPosition', $container['taskPosition'], 'movePosition');

$server->register('createTask', function($title, $project_id, $color_id = '', $column_id = 0, $owner_id = 0, $creator_id = 0, $date_due = '', $description = '', $category_id = 0, $score = 0, $swimlane_id = 0) use ($container) {

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
        'swimlane_id' => $swimlane_id,
    );

    list($valid,) = $container['taskValidator']->validateCreation($values);

    if (! $valid) {
        return false;
    }

    return $container['taskCreation']->create($values);
});

$server->register('updateTask', function($id, $title = null, $project_id = null, $color_id = null, $column_id = null, $owner_id = null, $creator_id = null, $date_due = null, $description = null, $category_id = null, $score = null, $swimlane_id = null) use ($container) {

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
        'swimlane_id' => $swimlane_id,
    );

    foreach ($values as $key => $value) {
        if (is_null($value)) {
            unset($values[$key]);
        }
    }

    list($valid) = $container['taskValidator']->validateApiModification($values);
    return $valid && $container['taskModification']->update($values);
});


/**
 * User procedures
 */
$server->bind('getUser', $container['user'], 'getById');
$server->bind('getAllUsers', $container['user'], 'getAll');
$server->bind('removeUser', $container['user'], 'remove');

$server->register('createUser', function($username, $password, $name = '', $email = '', $is_admin = 0, $default_project_id = 0) use ($container) {

    $values = array(
        'username' => $username,
        'password' => $password,
        'confirmation' => $password,
        'name' => $name,
        'email' => $email,
        'is_admin' => $is_admin,
        'default_project_id' => $default_project_id,
    );

    list($valid,) = $container['user']->validateCreation($values);

    if (! $valid) {
        return false;
    }

    return $container['user']->create($values);
});

$server->register('createLdapUser', function($username = '', $email = '', $is_admin = 0, $default_project_id = 0) use ($container) {

    $ldap = new Auth\Ldap($container);
    $res = $ldap->lookup($username, $email);

    if (!$res)
        return false;

    $values = array(
        'username' => $res['username'],
        'name' => $res['name'],
        'email' => $res['email'],
        'is_ldap_user' => 1,
        'is_admin' => $is_admin,
        'default_project_id' => $default_project_id,
    );

    return $container['user']->create($values);
});

$server->register('updateUser', function($id, $username = null, $name = null, $email = null, $is_admin = null, $default_project_id = null) use ($container) {

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

    list($valid,) = $container['user']->validateApiModification($values);
    return $valid && $container['user']->update($values);
});

/**
 * Category procedures
 */
$server->bind('getCategory', $container['category'], 'getById');
$server->bind('getAllCategories', $container['category'], 'getAll');
$server->bind('removeCategory', $container['category'], 'remove');

$server->register('createCategory', function($project_id, $name) use ($container) {

    $values = array(
        'project_id' => $project_id,
        'name' => $name,
    );

    list($valid,) = $container['category']->validateCreation($values);

    if (! $valid) {
        return false;
    }

    return $container['category']->create($values);
});

$server->register('updateCategory', function($id, $name) use ($container) {

    $values = array(
        'id' => $id,
        'name' => $name,
    );

    list($valid,) = $container['category']->validateModification($values);
    return $valid && $container['category']->update($values);
});

/**
 * Comments procedures
 */
$server->bind('getComment', $container['comment'], 'getById');
$server->bind('getAllComments', $container['comment'], 'getAll');
$server->bind('removeComment', $container['comment'], 'remove');

$server->register('createComment', function($task_id, $user_id, $content) use ($container) {

    $values = array(
        'task_id' => $task_id,
        'user_id' => $user_id,
        'comment' => $content,
    );

    list($valid,) = $container['comment']->validateCreation($values);

    if (! $valid) {
        return false;
    }

    return $container['comment']->create($values);
});

$server->register('updateComment', function($id, $content) use ($container) {

    $values = array(
        'id' => $id,
        'comment' => $content,
    );

    list($valid,) = $container['comment']->validateModification($values);
    return $valid && $container['comment']->update($values);
});

/**
 * Subtask procedures
 */
$server->bind('getSubtask', $container['subTask'], 'getById');
$server->bind('getAllSubtasks', $container['subTask'], 'getAll');
$server->bind('removeSubtask', $container['subTask'], 'remove');

$server->register('createSubtask', function($task_id, $title, $user_id = 0, $time_estimated = 0, $time_spent = 0, $status = 0) use ($container) {

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
    list($valid,) = $container['subTask']->validateCreation($values);

    if (! $valid) {
        return false;
    }

    return $container['subTask']->create($values);
});

$server->register('updateSubtask', function($id, $task_id, $title = null, $user_id = null, $time_estimated = null, $time_spent = null, $status = null) use ($container) {

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

    list($valid,) = $container['subTask']->validateApiModification($values);
    return $valid && $container['subTask']->update($values);
});

/**
 * Application procedures
 */
$server->register('getTimezone', function() use ($container) {
    return $container['config']->get('application_timezone');
});

/**
 * Parse incoming requests
 */
echo $server->execute();
