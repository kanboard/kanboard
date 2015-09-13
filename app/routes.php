<?php

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
$container['router']->addRoute('public/task/:task_id/:token', 'task', 'readonly', array('task_id', 'token'));

$container['router']->addRoute('project/:project_id/task/:task_id/activity', 'activity', 'task', array('project_id', 'task_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/screenshot', 'file', 'screenshot', array('project_id', 'task_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/upload', 'file', 'create', array('project_id', 'task_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/comment', 'comment', 'create', array('project_id', 'task_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/link', 'tasklink', 'create', array('project_id', 'task_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/transitions', 'task', 'transitions', array('project_id', 'task_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/analytics', 'task', 'analytics', array('project_id', 'task_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/remove', 'task', 'remove', array('project_id', 'task_id'));

$container['router']->addRoute('project/:project_id/task/:task_id/edit', 'taskmodification', 'edit', array('project_id', 'task_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/description', 'taskmodification', 'description', array('project_id', 'task_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/recurrence', 'taskmodification', 'recurrence', array('project_id', 'task_id'));

$container['router']->addRoute('project/:project_id/task/:task_id/close', 'taskstatus', 'close', array('task_id', 'project_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/open', 'taskstatus', 'open', array('task_id', 'project_id'));

$container['router']->addRoute('project/:project_id/task/:task_id/duplicate', 'taskduplication', 'duplicate', array('task_id', 'project_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/copy', 'taskduplication', 'copy', array('task_id', 'project_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/copy/:dst_project_id', 'taskduplication', 'copy', array('task_id', 'project_id', 'dst_project_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/move', 'taskduplication', 'move', array('task_id', 'project_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/move/:dst_project_id', 'taskduplication', 'move', array('task_id', 'project_id', 'dst_project_id'));

// Board routes
$container['router']->addRoute('board/:project_id', 'board', 'show', array('project_id'));
$container['router']->addRoute('b/:project_id', 'board', 'show', array('project_id'));
$container['router']->addRoute('public/board/:token', 'board', 'readonly', array('token'));

// Calendar routes
$container['router']->addRoute('calendar/:project_id', 'calendar', 'show', array('project_id'));
$container['router']->addRoute('c/:project_id', 'calendar', 'show', array('project_id'));

// Listing routes
$container['router']->addRoute('list/:project_id', 'listing', 'show', array('project_id'));
$container['router']->addRoute('l/:project_id', 'listing', 'show', array('project_id'));

// Gantt routes
$container['router']->addRoute('gantt/:project_id', 'gantt', 'project', array('project_id'));
$container['router']->addRoute('gantt/:project_id/sort/:sorting', 'gantt', 'project', array('project_id', 'sorting'));

// Subtask routes
$container['router']->addRoute('project/:project_id/task/:task_id/subtask/create', 'subtask', 'create', array('project_id', 'task_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/subtask/:subtask_id/remove', 'subtask', 'confirm', array('project_id', 'task_id', 'subtask_id'));
$container['router']->addRoute('project/:project_id/task/:task_id/subtask/:subtask_id/edit', 'subtask', 'edit', array('project_id', 'task_id', 'subtask_id'));

// Feed routes
$container['router']->addRoute('feed/project/:token', 'feed', 'project', array('token'));
$container['router']->addRoute('feed/user/:token', 'feed', 'user', array('token'));

// Ical routes
$container['router']->addRoute('ical/project/:token', 'ical', 'project', array('token'));
$container['router']->addRoute('ical/user/:token', 'ical', 'user', array('token'));

// Auth routes
$container['router']->addRoute('oauth/google', 'oauth', 'google');
$container['router']->addRoute('oauth/github', 'oauth', 'github');
$container['router']->addRoute('oauth/gitlab', 'oauth', 'gitlab');
$container['router']->addRoute('login', 'auth', 'login');
$container['router']->addRoute('logout', 'auth', 'logout');
