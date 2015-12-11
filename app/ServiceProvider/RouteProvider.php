<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\Http\Router;

/**
 * Route Provider
 *
 * @package serviceProvider
 * @author  Frederic Guillot
 */
class RouteProvider implements ServiceProviderInterface
{
    /**
     * Register providers
     *
     * @access public
     * @param  \Pimple\Container $container
     * @return \Pimple\Container
     */
    public function register(Container $container)
    {
        $container['router'] = new Router($container);

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
            $container['router']->addRoute('project/create/private', 'project', 'createPrivate');
            $container['router']->addRoute('project/:project_id', 'project', 'show', array('project_id'));
            $container['router']->addRoute('p/:project_id', 'project', 'show', array('project_id'));
            $container['router']->addRoute('project/:project_id/customer-filter', 'customfilter', 'index', array('project_id'));
            $container['router']->addRoute('project/:project_id/share', 'project', 'share', array('project_id'));
            $container['router']->addRoute('project/:project_id/notifications', 'project', 'notifications', array('project_id'));
            $container['router']->addRoute('project/:project_id/edit', 'project', 'edit', array('project_id'));
            $container['router']->addRoute('project/:project_id/integrations', 'project', 'integrations', array('project_id'));
            $container['router']->addRoute('project/:project_id/duplicate', 'project', 'duplicate', array('project_id'));
            $container['router']->addRoute('project/:project_id/remove', 'project', 'remove', array('project_id'));
            $container['router']->addRoute('project/:project_id/disable', 'project', 'disable', array('project_id'));
            $container['router']->addRoute('project/:project_id/enable', 'project', 'enable', array('project_id'));
            $container['router']->addRoute('project/:project_id/permissions', 'ProjectPermission', 'index', array('project_id'));
            $container['router']->addRoute('project/:project_id/import', 'taskImport', 'step1', array('project_id'));

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

            // Exports
            $container['router']->addRoute('export/tasks/:project_id', 'export', 'tasks', array('project_id'));
            $container['router']->addRoute('export/subtasks/:project_id', 'export', 'subtasks', array('project_id'));
            $container['router']->addRoute('export/transitions/:project_id', 'export', 'transitions', array('project_id'));
            $container['router']->addRoute('export/summary/:project_id', 'export', 'summary', array('project_id'));

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

            // Users
            $container['router']->addRoute('users', 'user', 'index');
            $container['router']->addRoute('user/show/:user_id', 'user', 'show', array('user_id'));
            $container['router']->addRoute('user/show/:user_id/timesheet', 'user', 'timesheet', array('user_id'));
            $container['router']->addRoute('user/show/:user_id/last-logins', 'user', 'last', array('user_id'));
            $container['router']->addRoute('user/show/:user_id/sessions', 'user', 'sessions', array('user_id'));
            $container['router']->addRoute('user/:user_id/edit', 'user', 'edit', array('user_id'));
            $container['router']->addRoute('user/:user_id/password', 'user', 'password', array('user_id'));
            $container['router']->addRoute('user/:user_id/share', 'user', 'share', array('user_id'));
            $container['router']->addRoute('user/:user_id/notifications', 'user', 'notifications', array('user_id'));
            $container['router']->addRoute('user/:user_id/accounts', 'user', 'external', array('user_id'));
            $container['router']->addRoute('user/:user_id/integrations', 'user', 'integrations', array('user_id'));
            $container['router']->addRoute('user/:user_id/authentication', 'user', 'authentication', array('user_id'));
            $container['router']->addRoute('user/:user_id/remove', 'user', 'remove', array('user_id'));
            $container['router']->addRoute('user/:user_id/2fa', 'twofactor', 'index', array('user_id'));

            // Groups
            $container['router']->addRoute('groups', 'group', 'index');
            $container['router']->addRoute('groups/create', 'group', 'create');
            $container['router']->addRoute('group/:group_id/associate', 'group', 'associate', array('group_id'));
            $container['router']->addRoute('group/:group_id/dissociate/:user_id', 'group', 'dissociate', array('group_id', 'user_id'));
            $container['router']->addRoute('group/:group_id/edit', 'group', 'edit', array('group_id'));
            $container['router']->addRoute('group/:group_id/members', 'group', 'users', array('group_id'));
            $container['router']->addRoute('group/:group_id/remove', 'group', 'confirm', array('group_id'));

            // Config
            $container['router']->addRoute('settings', 'config', 'index');
            $container['router']->addRoute('settings/plugins', 'config', 'plugins');
            $container['router']->addRoute('settings/application', 'config', 'application');
            $container['router']->addRoute('settings/project', 'config', 'project');
            $container['router']->addRoute('settings/project', 'config', 'project');
            $container['router']->addRoute('settings/board', 'config', 'board');
            $container['router']->addRoute('settings/calendar', 'config', 'calendar');
            $container['router']->addRoute('settings/integrations', 'config', 'integrations');
            $container['router']->addRoute('settings/webhook', 'config', 'webhook');
            $container['router']->addRoute('settings/api', 'config', 'api');
            $container['router']->addRoute('settings/links', 'link', 'index');
            $container['router']->addRoute('settings/currencies', 'currency', 'index');

            // Doc
            $container['router']->addRoute('documentation/:file', 'doc', 'show', array('file'));
            $container['router']->addRoute('documentation', 'doc', 'show');

            // Auth routes
            $container['router']->addRoute('oauth/google', 'oauth', 'google');
            $container['router']->addRoute('oauth/github', 'oauth', 'github');
            $container['router']->addRoute('oauth/gitlab', 'oauth', 'gitlab');
            $container['router']->addRoute('login', 'auth', 'login');
            $container['router']->addRoute('logout', 'auth', 'logout');
        }

        return $container;
    }
}
