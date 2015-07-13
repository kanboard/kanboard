<?php

require dirname(__DIR__) . '/vendor/autoload.php';

// Automatically parse environment configuration (Heroku)
if (getenv('DATABASE_URL')) {

    $dbopts = parse_url(getenv('DATABASE_URL'));

    define('DB_DRIVER', $dbopts['scheme']);
    define('DB_USERNAME', $dbopts["user"]);
    define('DB_PASSWORD', $dbopts["pass"]);
    define('DB_HOSTNAME', $dbopts["host"]);
    define('DB_PORT', isset($dbopts["port"]) ? $dbopts["port"] : null);
    define('DB_NAME', ltrim($dbopts["path"], '/'));
}

// Include custom config file
if (file_exists('config.php')) {
    require 'config.php';
}

require __DIR__.'/constants.php';
require __DIR__.'/check_setup.php';

$container = new Pimple\Container;
$container->register(new ServiceProvider\LoggingProvider);
$container->register(new ServiceProvider\DatabaseProvider);
$container->register(new ServiceProvider\ClassProvider);
$container->register(new ServiceProvider\EventDispatcherProvider);

if (ENABLE_URL_REWRITE) {

    // Dashboard
    $container['router']->addRoute('dashboard', 'app', 'index');
    $container['router']->addRoute('dashboard/:user_id', 'app', 'index', array('user_id'));
    $container['router']->addRoute('dashboard/:user_id/projects', 'app', 'projects', array('user_id'));
    $container['router']->addRoute('dashboard/:user_id/tasks', 'app', 'tasks', array('user_id'));
    $container['router']->addRoute('dashboard/:user_id/subtasks', 'app', 'subtasks', array('user_id'));
    $container['router']->addRoute('dashboard/:user_id/calendar', 'app', 'calendar', array('user_id'));
    $container['router']->addRoute('dashboard/:user_id/activity', 'app', 'activity', array('user_id'));

    // Search routes
    $container['router']->addRoute('search', 'search', 'index');
    $container['router']->addRoute('search/:search', 'search', 'index', array('search'));

    // Project routes
    $container['router']->addRoute('projects', 'project', 'index');
    $container['router']->addRoute('project/create', 'project', 'create');
    $container['router']->addRoute('project/create/:private', 'project', 'create', array('private'));
    $container['router']->addRoute('project/:project_id', 'project', 'show', array('project_id'));
    $container['router']->addRoute('p/:project_id', 'project', 'show', array('project_id'));
    $container['router']->addRoute('project/:project_id/share', 'project', 'share', array('project_id'));
    $container['router']->addRoute('project/:project_id/edit', 'project', 'edit', array('project_id'));
    $container['router']->addRoute('project/:project_id/integration', 'project', 'integration', array('project_id'));
    $container['router']->addRoute('project/:project_id/users', 'project', 'users', array('project_id'));
    $container['router']->addRoute('project/:project_id/duplicate', 'project', 'duplicate', array('project_id'));
    $container['router']->addRoute('project/:project_id/remove', 'project', 'remove', array('project_id'));
    $container['router']->addRoute('project/:project_id/disable', 'project', 'disable', array('project_id'));
    $container['router']->addRoute('project/:project_id/enable', 'project', 'enable', array('project_id'));

    // Action routes
    $container['router']->addRoute('project/:project_id/actions', 'action', 'index', array('project_id'));
    $container['router']->addRoute('project/:project_id/action/:action_id/confirm', 'action', 'confirm', array('project_id', 'action_id'));

    // Column routes
    $container['router']->addRoute('project/:project_id/columns', 'column', 'index', array('project_id'));
    $container['router']->addRoute('project/:project_id/column/:column_id/edit', 'column', 'edit', array('project_id', 'column_id'));
    $container['router']->addRoute('project/:project_id/column/:column_id/confirm', 'column', 'confirm', array('project_id', 'column_id'));
    $container['router']->addRoute('project/:project_id/column/:column_id/move/:direction', 'column', 'move', array('project_id', 'column_id', 'direction'));

    // Swimlane routes
    $container['router']->addRoute('project/:project_id/swimlanes', 'swimlane', 'index', array('project_id'));
    $container['router']->addRoute('project/:project_id/swimlane/:swimlane_id/edit', 'swimlane', 'edit', array('project_id', 'swimlane_id'));
    $container['router']->addRoute('project/:project_id/swimlane/:swimlane_id/confirm', 'swimlane', 'confirm', array('project_id', 'swimlane_id'));
    $container['router']->addRoute('project/:project_id/swimlane/:swimlane_id/disable', 'swimlane', 'disable', array('project_id', 'swimlane_id'));
    $container['router']->addRoute('project/:project_id/swimlane/:swimlane_id/enable', 'swimlane', 'enable', array('project_id', 'swimlane_id'));
    $container['router']->addRoute('project/:project_id/swimlane/:swimlane_id/up', 'swimlane', 'moveup', array('project_id', 'swimlane_id'));
    $container['router']->addRoute('project/:project_id/swimlane/:swimlane_id/down', 'swimlane', 'movedown', array('project_id', 'swimlane_id'));

    // Category routes
    $container['router']->addRoute('project/:project_id/categories', 'category', 'index', array('project_id'));
    $container['router']->addRoute('project/:project_id/category/:category_id/edit', 'category', 'edit', array('project_id', 'category_id'));
    $container['router']->addRoute('project/:project_id/category/:category_id/confirm', 'category', 'confirm', array('project_id', 'category_id'));

    // Task routes
    $container['router']->addRoute('project/:project_id/task/:task_id', 'task', 'show', array('project_id', 'task_id'));
    $container['router']->addRoute('t/:task_id', 'task', 'show', array('task_id'));
    $container['router']->addRoute('project/:project_id/swimlane/:swimlane_id/column/:column_id', 'task', 'create', array('project_id', 'swimlane_id', 'column_id'));
    $container['router']->addRoute('public/task/:task_id/:token', 'task', 'readonly', array('task_id', 'token'));

    // Board routes
    $container['router']->addRoute('board/:project_id', 'board', 'show', array('project_id'));
    $container['router']->addRoute('b/:project_id', 'board', 'show', array('project_id'));
    $container['router']->addRoute('board/:project_id/filter/:search', 'board', 'show', array('project_id', 'search'));
    $container['router']->addRoute('public/board/:token', 'board', 'readonly', array('token'));

    // Calendar routes
    $container['router']->addRoute('calendar/:project_id', 'calendar', 'show', array('project_id'));
    $container['router']->addRoute('c/:project_id', 'calendar', 'show', array('project_id'));
    $container['router']->addRoute('calendar/:project_id/:search', 'calendar', 'show', array('project_id', 'search'));

    // Listing routes
    $container['router']->addRoute('list/:project_id', 'listing', 'show', array('project_id'));
    $container['router']->addRoute('l/:project_id', 'listing', 'show', array('project_id'));
    $container['router']->addRoute('list/:project_id/:search', 'listing', 'show', array('project_id', 'search'));

    // Subtask routes
    $container['router']->addRoute('project/:project_id/task/:task_id/subtask/:subtask_id', 'subtask', 'remove', array('project_id', 'task_id', 'subtask_id'));

    // Feed routes
    $container['router']->addRoute('feed/project/:token', 'feed', 'project', array('token'));
    $container['router']->addRoute('feed/user/:token', 'feed', 'user', array('token'));

    // Ical routes
    $container['router']->addRoute('ical/project/:token', 'ical', 'project', array('token'));
    $container['router']->addRoute('ical/user/:token', 'ical', 'user', array('token'));
}
