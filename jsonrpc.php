<?php

require __DIR__.'/app/common.php';

use JsonRPC\Server;
use Model\Project;
use Model\ProjectPermission;
use Model\Task;
use Model\TaskFinder;
use Model\TaskValidator;
use Model\User;
use Model\Config;
use Model\Category;
use Model\Comment;
use Model\SubTask;
use Model\Board;
use Model\Action;
use Model\Webhook;
use Model\Notification;

$config = new Config($registry);
$config->setupTranslations();
$config->setupTimezone();

$project = new Project($registry);
$projectPermission = new ProjectPermission($registry);
$task = new Task($registry);
$taskFinder = new TaskFinder($registry);
$taskValidator = new TaskValidator($registry);
$user = new User($registry);
$category = new Category($registry);
$comment = new Comment($registry);
$subtask = new SubTask($registry);
$board = new Board($registry);
$action = new Action($registry);
$webhook = new Webhook($registry);
$notification = new Notification($registry);

$action->attachEvents();
$project->attachEvents();
$webhook->attachEvents();
$notification->attachEvents();

$server = new Server;
$server->authentication(array('jsonrpc' => $config->get('api_token')));

/**
 * Project procedures
 */
$server->register('createProject', function($name) use ($project) {
    $values = array('name' => $name);
    list($valid,) = $project->validateCreation($values);
    return $valid && $project->create($values);
});

$server->register('getProjectById', function($project_id) use ($project) {
    return $project->getById($project_id);
});

$server->register('getProjectByName', function($name) use ($project) {
    return $project->getByName($name);
});

$server->register('getAllProjects', function() use ($project) {
    return $project->getAll();
});

$server->register('updateProject', function($id, $name, $is_active = null, $is_public = null, $token = null) use ($project) {

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

    list($valid,) = $project->validateModification($values);
    return $valid && $project->update($values);
});

$server->register('removeProject', function($project_id) use ($project) {
    return $project->remove($project_id);
});

$server->register('enableProject', function($project_id) use ($project) {
    return $project->enable($project_id);
});

$server->register('disableProject', function($project_id) use ($project) {
    return $project->disable($project_id);
});

$server->register('enableProjectPublicAccess', function($project_id) use ($project) {
    return $project->enablePublicAccess($project_id);
});

$server->register('disableProjectPublicAccess', function($project_id) use ($project) {
    return $project->disablePublicAccess($project_id);
});


/**
 * Board procedures
 */
$server->register('getBoard', function($project_id) use ($board) {
    return $board->get($project_id);
});

$server->register('getColumns', function($project_id) use ($board) {
    return $board->getColumns($project_id);
});

$server->register('getColumn', function($column_id) use ($board) {
    return $board->getColumn($column_id);
});

$server->register('moveColumnUp', function($project_id, $column_id) use ($board) {
    return $board->moveUp($project_id, $column_id);
});

$server->register('moveColumnDown', function($project_id, $column_id) use ($board) {
    return $board->moveDown($project_id, $column_id);
});

$server->register('updateColumn', function($column_id, $title, $task_limit = 0) use ($board) {
    return $board->updateColumn($column_id, $title, $task_limit);
});

$server->register('addColumn', function($project_id, $title, $task_limit = 0) use ($board) {
    return $board->addColumn($project_id, $title, $task_limit);
});

$server->register('removeColumn', function($column_id) use ($board) {
    return $board->removeColumn($column_id);
});


/**
 * Project permissions procedures
 */
$server->register('getAllowedUsers', function($project_id) use ($projectPermission) {
    return $projectPermission->getMemberList($project_id, false, false);
});

$server->register('revokeUser', function($project_id, $user_id) use ($project, $projectPermission) {
    return $projectPermission->revokeUser($project_id, $user_id);
});

$server->register('allowUser', function($project_id, $user_id) use ($project, $projectPermission) {
    return $projectPermission->allowUser($project_id, $user_id);
});


/**
 * Task procedures
 */
$server->register('createTask', function($title, $project_id, $color_id = '', $column_id = 0, $owner_id = 0, $creator_id = 0, $date_due = '', $description = '', $category_id = 0, $score = 0) use ($task, $taskValidator) {

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

    list($valid,) = $taskValidator->validateCreation($values);
    return $valid && $task->create($values) !== false;
});

$server->register('getTask', function($task_id) use ($taskFinder) {
    return $taskFinder->getById($task_id);
});

$server->register('getAllTasks', function($project_id, $status) use ($taskFinder) {
    return $taskFinder->getAll($project_id, $status);
});

$server->register('updateTask', function($id, $title = null, $project_id = null, $color_id = null, $column_id = null, $owner_id = null, $creator_id = null, $date_due = null, $description = null, $category_id = null, $score = null) use ($task, $taskValidator) {

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

    list($valid) = $taskValidator->validateApiModification($values);
    return $valid && $task->update($values);
});

$server->register('openTask', function($task_id) use ($task) {
    return $task->open($task_id);
});

$server->register('closeTask', function($task_id) use ($task) {
    return $task->close($task_id);
});

$server->register('removeTask', function($task_id) use ($task) {
    return $task->remove($task_id);
});

$server->register('moveTaskPosition', function($project_id, $task_id, $column_id, $position) use ($task) {
    return $task->movePosition($project_id, $task_id, $column_id, $position);
});


/**
 * User procedures
 */
$server->register('createUser', function($username, $password, $name = '', $email = '', $is_admin = 0, $default_project_id = 0) use ($user) {

    $values = array(
        'username' => $username,
        'password' => $password,
        'confirmation' => $password,
        'name' => $name,
        'email' => $email,
        'is_admin' => $is_admin,
        'default_project_id' => $default_project_id,
    );

    list($valid,) = $user->validateCreation($values);
    return $valid && $user->create($values);
});

$server->register('getUser', function($user_id) use ($user) {
    return $user->getById($user_id);
});

$server->register('getAllUsers', function() use ($user) {
    return $user->getAll();
});

$server->register('updateUser', function($id, $username = null, $name = null, $email = null, $is_admin = null, $default_project_id = null) use ($user) {

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

    list($valid,) = $user->validateApiModification($values);
    return $valid && $user->update($values);
});

$server->register('removeUser', function($user_id) use ($user) {
    return $user->remove($user_id);
});


/**
 * Category procedures
 */
$server->register('createCategory', function($project_id, $name) use ($category) {

    $values = array(
        'project_id' => $project_id,
        'name' => $name,
    );

    list($valid,) = $category->validateCreation($values);
    return $valid && $category->create($values);
});

$server->register('getCategory', function($category_id) use ($category) {
    return $category->getById($category_id);
});

$server->register('getAllCategories', function($project_id) use ($category) {
    return $category->getAll($project_id);
});

$server->register('updateCategory', function($id, $name) use ($category) {

    $values = array(
        'id' => $id,
        'name' => $name,
    );

    list($valid,) = $category->validateModification($values);
    return $valid && $category->update($values);
});

$server->register('removeCategory', function($category_id) use ($category) {
    return $category->remove($category_id);
});


/**
 * Comments procedures
 */
$server->register('createComment', function($task_id, $user_id, $content) use ($comment) {

    $values = array(
        'task_id' => $task_id,
        'user_id' => $user_id,
        'comment' => $content,
    );

    list($valid,) = $comment->validateCreation($values);
    return $valid && $comment->create($values);
});

$server->register('getComment', function($comment_id) use ($comment) {
    return $comment->getById($comment_id);
});

$server->register('getAllComments', function($task_id) use ($comment) {
    return $comment->getAll($task_id);
});

$server->register('updateComment', function($id, $content) use ($comment) {

    $values = array(
        'id' => $id,
        'comment' => $content,
    );

    list($valid,) = $comment->validateModification($values);
    return $valid && $comment->update($values);
});

$server->register('removeComment', function($comment_id) use ($comment) {
    return $comment->remove($comment_id);
});


/**
 * Subtask procedures
 */
$server->register('createSubtask', function($task_id, $title, $user_id = 0, $time_estimated = 0, $time_spent = 0, $status = 0) use ($subtask) {

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

    list($valid,) = $subtask->validateCreation($values);
    return $valid && $subtask->create($values);
});

$server->register('getSubtask', function($subtask_id) use ($subtask) {
    return $subtask->getById($subtask_id);
});

$server->register('getAllSubtasks', function($task_id) use ($subtask) {
    return $subtask->getAll($task_id);
});

$server->register('updateSubtask', function($id, $task_id, $title = null, $user_id = null, $time_estimated = null, $time_spent = null, $status = null) use ($subtask) {

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

    list($valid,) = $subtask->validateModification($values);
    return $valid && $subtask->update($values);
});

$server->register('removeSubtask', function($subtask_id) use ($subtask) {
    return $subtask->remove($subtask_id);
});


/**
 * Parse incoming requests
 */
echo $server->execute();
