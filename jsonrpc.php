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
$server->bind('getProjectActivity', $container['projectActivity'], 'getProjects');

$server->register('createProject', function($name, $description = null) use ($container) {
    $values = array(
        'name' => $name,
        'description' => $description
    );
    list($valid,) = $container['project']->validateCreation($values);
    return $valid ? $container['project']->create($values) : false;
});

$server->register('updateProject', function($id, $name, $is_active = null, $is_public = null, $token = null, $description = null) use ($container) {

    $values = array(
        'id' => $id,
        'name' => $name,
        'is_active' => $is_active,
        'is_public' => $is_public,
        'token' => $token,
        'description' => $description
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
 * Swimlane procedures
 */
$server->bind('getSwimlanes', $container['swimlane'], 'getSwimlanes');
$server->bind('getAllSwimlanes', $container['swimlane'], 'getAll');
$server->bind('getSwimlane', $container['swimlane'], 'getByName');
$server->bind('addSwimlane', $container['swimlane'], 'create');
$server->bind('updateSwimlane', $container['swimlane'], 'rename');
$server->bind('removeSwimlane', $container['swimlane'], 'remove');
$server->bind('disableSwimlane', $container['swimlane'], 'disable');
$server->bind('enableSwimlane', $container['swimlane'], 'enable');
$server->bind('moveSwimlaneUp', $container['swimlane'], 'moveUp');
$server->bind('moveSwimlaneDown', $container['swimlane'], 'moveDown');

/**
 * Actions procedures
 */
$server->bind('getAvailableActions', $container['action'], 'getAvailableActions');
$server->bind('getAvailableEvents', $container['action'], 'getAvailableEvents');
$server->bind('getCompatibleEvents', $container['action'], 'getCompatibleEvents');
$server->bind('removeAction', $container['action'], 'remove');

$server->register('getActions', function($project_id) use ($container) {
    $actions = $container['action']->getAllByProject($project_id);

    foreach ($actions as $index => $action) {
        $params = array();

        foreach($action['params'] as $param) {
            $params[$param['name']] = $param['value'];
        }

        $actions[$index]['params'] = $params;
    }

    return $actions;
});

$server->register('createAction', function($project_id, $event_name, $action_name, $params) use ($container) {

    $values = array(
        'project_id' => $project_id,
        'event_name' => $event_name,
        'action_name' => $action_name,
        'params' => $params,
    );

    list($valid,) = $container['action']->validateCreation($values);

    if (! $valid) {
        return false;
    }

    // Check the action exists
    $actions = $container['action']->getAvailableActions();

    if (! isset($actions[$action_name])) {
        return false;
    }

    // Check the event
    $action = $container['action']->load($action_name, $project_id, $event_name);

    if (! in_array($event_name, $action->getCompatibleEvents())) {
        return false;
    }

    $required_params = $action->getActionRequiredParameters();

    // Check missing parameters
    foreach($required_params as $param => $value) {
        if (! isset($params[$param])) {
            return false;
        }
    }

    // Check extra parameters
    foreach($params as $param => $value) {
        if (! isset($required_params[$param])) {
            return false;
        }
    }

    return $container['action']->create($values);
});


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
$server->bind('getOverdueTasks', $container['taskFinder'], 'getOverdueTasks');
$server->bind('openTask', $container['taskStatus'], 'open');
$server->bind('closeTask', $container['taskStatus'], 'close');
$server->bind('removeTask', $container['task'], 'remove');
$server->bind('moveTaskPosition', $container['taskPosition'], 'movePosition');

$server->register('createTask', function($title, $project_id, $color_id = '', $column_id = 0, $owner_id = 0, $creator_id = 0, $date_due = '', $description = '', $category_id = 0, $score = 0, $swimlane_id = 0, $recurrence_status = 0, $recurrence_trigger = 0, $recurrence_factor = 0, $recurrence_timeframe = 0, $recurrence_basedate = 0) use ($container) {

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
        'recurrence_status' => $recurrence_status,
        'recurrence_trigger' => $recurrence_trigger,
        'recurrence_factor' => $recurrence_factor,
        'recurrence_timeframe' => $recurrence_timeframe,
        'recurrence_basedate' => $recurrence_basedate,
    );

    list($valid,) = $container['taskValidator']->validateCreation($values);

    if (! $valid) {
        return false;
    }

    return $container['taskCreation']->create($values);
});

$server->register('updateTask', function($id, $title = null, $project_id = null, $color_id = null, $column_id = null, $owner_id = null, $creator_id = null, $date_due = null, $description = null, $category_id = null, $score = null, $swimlane_id = null, $recurrence_status = null, $recurrence_trigger = null, $recurrence_factor = null, $recurrence_timeframe = null, $recurrence_basedate = null) use ($container) {

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
        'recurrence_status' => $recurrence_status,
        'recurrence_trigger' => $recurrence_trigger,
        'recurrence_factor' => $recurrence_factor,
        'recurrence_timeframe' => $recurrence_timeframe,
        'recurrence_basedate' => $recurrence_basedate,
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
    $user = $ldap->lookup($username, $email);

    if (! $user) {
        return false;
    }

    $values = array(
        'username' => $user['username'],
        'name' => $user['name'],
        'email' => $user['email'],
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
$server->bind('getSubtask', $container['subtask'], 'getById');
$server->bind('getAllSubtasks', $container['subtask'], 'getAll');
$server->bind('removeSubtask', $container['subtask'], 'remove');

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
    list($valid,) = $container['subtask']->validateCreation($values);

    if (! $valid) {
        return false;
    }

    return $container['subtask']->create($values);
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

    list($valid,) = $container['subtask']->validateApiModification($values);
    return $valid && $container['subtask']->update($values);
});

/**
 * Application procedures
 */
$server->register('getTimezone', function() use ($container) {
    return $container['config']->get('application_timezone');
});

$server->register('getVersion', function() use ($container) {
    return APP_VERSION;
});

/**
 * Parse incoming requests
 */
echo $server->execute();
