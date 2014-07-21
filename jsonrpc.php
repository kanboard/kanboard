<?php

require __DIR__.'/app/common.php';
require __DIR__.'/vendor/JsonRPC/Server.php';

use JsonRPC\Server;
use Model\Project;
use Model\Task;
use Model\User;
use Model\Config;
use Model\Category;
use Model\Comment;
use Model\SubTask;
use Model\Board;
use Model\Action;
use Model\Webhook;

$config = new Config($registry->shared('db'), $registry->shared('event'));
$project = new Project($registry->shared('db'), $registry->shared('event'));
$task = new Task($registry->shared('db'), $registry->shared('event'));
$user = new User($registry->shared('db'), $registry->shared('event'));
$category = new Category($registry->shared('db'), $registry->shared('event'));
$comment = new Comment($registry->shared('db'), $registry->shared('event'));
$subtask = new SubTask($registry->shared('db'), $registry->shared('event'));
$board = new Board($registry->shared('db'), $registry->shared('event'));
$action = new Action($registry->shared('db'), $registry->shared('event'));
$webhook = new Webhook($registry->shared('db'), $registry->shared('event'));

$action->attachEvents();
$project->attachEvents();
$webhook->attachEvents();

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

$server->register('updateProject', function(array $values) use ($project) {
    list($valid,) = $project->validateModification($values);
    return $valid && $project->update($values);
});

$server->register('removeProject', function($project_id) use ($project) {
    return $project->remove($project_id);
});

$server->register('getBoard', function($project_id) use ($board) {
    return $board->get($project_id);
});

$server->register('getColumns', function($project_id) use ($board) {
    return $board->getColumns($project_id);
});

$server->register('moveColumnUp', function($project_id, $column_id) use ($board) {
    return $board->moveUp($project_id, $column_id);
});

$server->register('moveColumnDown', function($project_id, $column_id) use ($board) {
    return $board->moveDown($project_id, $column_id);
});

$server->register('updateColumn', function($column_id, array $values) use ($board) {
    return $board->updateColumn($column_id, $values);
});

$server->register('addColumn', function($project_id, array $values) use ($board) {
    $values += array('project_id' => $project_id);
    return $board->add($values);
});

$server->register('removeColumn', function($column_id) use ($board) {
    return $board->removeColumn($column_id);
});

$server->register('getAllowedUsers', function($project_id) use ($project) {
    return $project->getUsersList($project_id, false, false);
});

$server->register('revokeUser', function($project_id, $user_id) use ($project) {
    return $project->revokeUser($project_id, $user_id);
});

$server->register('allowUser', function($project_id, $user_id) use ($project) {
    return $project->allowUser($project_id, $user_id);
});


/**
 * Task procedures
 */
$server->register('createTask', function(array $values) use ($task) {
    list($valid,) = $task->validateCreation($values);
    return $valid && $task->create($values) !== false;
});

$server->register('getTask', function($task_id) use ($task) {
    return $task->getById($task_id);
});

$server->register('getAllTasks', function($project_id, array $status) use ($task) {
    return $task->getAll($project_id, $status);
});

$server->register('updateTask', function($values) use ($task) {
    list($valid,) = $task->validateModification($values);
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


/**
 * User procedures
 */
$server->register('createUser', function(array $values) use ($user) {
    list($valid,) = $user->validateCreation($values);
    return $valid && $user->create($values);
});

$server->register('getUser', function($user_id) use ($user) {
    return $user->getById($user_id);
});

$server->register('getAllUsers', function() use ($user) {
    return $user->getAll();
});

$server->register('updateUser', function($values) use ($user) {
    list($valid,) = $user->validateModification($values);
    return $valid && $user->update($values);
});

$server->register('removeUser', function($user_id) use ($user) {
    return $user->remove($user_id);
});


/**
 * Category procedures
 */
$server->register('createCategory', function(array $values) use ($category) {
    list($valid,) = $category->validateCreation($values);
    return $valid && $category->create($values);
});

$server->register('getCategory', function($category_id) use ($category) {
    return $category->getById($category_id);
});

$server->register('getAllCategories', function($project_id) use ($category) {
    return $category->getAll($project_id);
});

$server->register('updateCategory', function($values) use ($category) {
    list($valid,) = $category->validateModification($values);
    return $valid && $category->update($values);
});

$server->register('removeCategory', function($category_id) use ($category) {
    return $category->remove($category_id);
});


/**
 * Comments procedures
 */
$server->register('createComment', function(array $values) use ($comment) {
    list($valid,) = $comment->validateCreation($values);
    return $valid && $comment->create($values);
});

$server->register('getComment', function($comment_id) use ($comment) {
    return $comment->getById($comment_id);
});

$server->register('getAllComments', function($task_id) use ($comment) {
    return $comment->getAll($task_id);
});

$server->register('updateComment', function($values) use ($comment) {
    list($valid,) = $comment->validateModification($values);
    return $valid && $comment->update($values);
});

$server->register('removeComment', function($comment_id) use ($comment) {
    return $comment->remove($comment_id);
});


/**
 * Subtask procedures
 */
$server->register('createSubtask', function(array $values) use ($subtask) {
    list($valid,) = $subtask->validate($values);
    return $valid && $subtask->create($values);
});

$server->register('getSubtask', function($subtask_id) use ($subtask) {
    return $subtask->getById($subtask_id);
});

$server->register('getAllSubtasks', function($task_id) use ($subtask) {
    return $subtask->getAll($task_id);
});

$server->register('updateSubtask', function($values) use ($subtask) {
    list($valid,) = $subtask->validate($values);
    return $valid && $subtask->update($values);
});

$server->register('removeSubtask', function($subtask_id) use ($subtask) {
    return $subtask->remove($subtask_id);
});


/**
 * Parse incoming requests
 */
echo $server->execute();
