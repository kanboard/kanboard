<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\Http\Route;
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
        $container['route'] = new Route($container);

        if (ENABLE_URL_REWRITE) {
            $container['route']->enable();

            // Dashboard
            $container['route']->addRoute('dashboard', 'DashboardController', 'show');
            $container['route']->addRoute('dashboard/:user_id', 'DashboardController', 'show');
            $container['route']->addRoute('dashboard/:user_id/projects', 'DashboardController', 'projects');
            $container['route']->addRoute('dashboard/:user_id/tasks', 'DashboardController', 'tasks');
            $container['route']->addRoute('dashboard/:user_id/subtasks', 'DashboardController', 'subtasks');
            $container['route']->addRoute('dashboard/:user_id/calendar', 'DashboardController', 'calendar');
            $container['route']->addRoute('dashboard/:user_id/activity', 'DashboardController', 'activity');
            $container['route']->addRoute('dashboard/:user_id/notifications', 'DashboardController', 'notifications');

            // Search routes
            $container['route']->addRoute('search', 'search', 'index');
            $container['route']->addRoute('search/activity', 'search', 'activity');

            // ProjectCreation routes
            $container['route']->addRoute('project/create', 'ProjectCreation', 'create');
            $container['route']->addRoute('project/create/private', 'ProjectCreation', 'createPrivate');

            // Project routes
            $container['route']->addRoute('projects', 'project', 'index');
            $container['route']->addRoute('project/:project_id', 'project', 'show');
            $container['route']->addRoute('p/:project_id', 'project', 'show');
            $container['route']->addRoute('project/:project_id/customer-filters', 'customfilter', 'index');
            $container['route']->addRoute('project/:project_id/share', 'project', 'share');
            $container['route']->addRoute('project/:project_id/notifications', 'project', 'notifications');
            $container['route']->addRoute('project/:project_id/integrations', 'project', 'integrations');
            $container['route']->addRoute('project/:project_id/duplicate', 'project', 'duplicate');
            $container['route']->addRoute('project/:project_id/remove', 'project', 'remove');
            $container['route']->addRoute('project/:project_id/disable', 'project', 'disable');
            $container['route']->addRoute('project/:project_id/enable', 'project', 'enable');
            $container['route']->addRoute('project/:project_id/permissions', 'ProjectPermission', 'index');
            $container['route']->addRoute('project/:project_id/import', 'taskImport', 'step1');
            $container['route']->addRoute('project/:project_id/activity', 'activity', 'project');

            // Project Overview
            $container['route']->addRoute('project/:project_id/overview', 'ProjectOverview', 'show');

            // ProjectEdit routes
            $container['route']->addRoute('project/:project_id/edit', 'ProjectEdit', 'edit');
            $container['route']->addRoute('project/:project_id/edit/dates', 'ProjectEdit', 'dates');
            $container['route']->addRoute('project/:project_id/edit/description', 'ProjectEdit', 'description');
            $container['route']->addRoute('project/:project_id/edit/priority', 'ProjectEdit', 'priority');

            // ProjectUser routes
            $container['route']->addRoute('projects/managers/:user_id', 'projectuser', 'managers');
            $container['route']->addRoute('projects/members/:user_id', 'projectuser', 'members');
            $container['route']->addRoute('projects/tasks/:user_id/opens', 'projectuser', 'opens');
            $container['route']->addRoute('projects/tasks/:user_id/closed', 'projectuser', 'closed');
            $container['route']->addRoute('projects/managers', 'projectuser', 'managers');

            // Action routes
            $container['route']->addRoute('project/:project_id/actions', 'action', 'index');

            // Column routes
            $container['route']->addRoute('project/:project_id/columns', 'column', 'index');

            // Swimlane routes
            $container['route']->addRoute('project/:project_id/swimlanes', 'swimlane', 'index');

            // Category routes
            $container['route']->addRoute('project/:project_id/categories', 'category', 'index');

            // Task routes
            $container['route']->addRoute('project/:project_id/task/:task_id', 'task', 'show');
            $container['route']->addRoute('t/:task_id', 'task', 'show');
            $container['route']->addRoute('public/task/:task_id/:token', 'task', 'readonly');

            $container['route']->addRoute('project/:project_id/task/:task_id/activity', 'activity', 'task');
            $container['route']->addRoute('project/:project_id/task/:task_id/transitions', 'task', 'transitions');
            $container['route']->addRoute('project/:project_id/task/:task_id/analytics', 'task', 'analytics');
            $container['route']->addRoute('project/:project_id/task/:task_id/time-tracking', 'task', 'timetracking');

            // Exports
            $container['route']->addRoute('export/tasks/:project_id', 'export', 'tasks');
            $container['route']->addRoute('export/subtasks/:project_id', 'export', 'subtasks');
            $container['route']->addRoute('export/transitions/:project_id', 'export', 'transitions');
            $container['route']->addRoute('export/summary/:project_id', 'export', 'summary');

            // Analytics routes
            $container['route']->addRoute('analytics/tasks/:project_id', 'analytic', 'tasks');
            $container['route']->addRoute('analytics/users/:project_id', 'analytic', 'users');
            $container['route']->addRoute('analytics/cfd/:project_id', 'analytic', 'cfd');
            $container['route']->addRoute('analytics/burndown/:project_id', 'analytic', 'burndown');
            $container['route']->addRoute('analytics/average-time-column/:project_id', 'analytic', 'averageTimeByColumn');
            $container['route']->addRoute('analytics/lead-cycle-time/:project_id', 'analytic', 'leadAndCycleTime');
            $container['route']->addRoute('analytics/estimated-spent-time/:project_id', 'analytic', 'compareHours');

            // Board routes
            $container['route']->addRoute('board/:project_id', 'board', 'show');
            $container['route']->addRoute('b/:project_id', 'board', 'show');
            $container['route']->addRoute('public/board/:token', 'board', 'readonly');

            // Calendar routes
            $container['route']->addRoute('calendar/:project_id', 'calendar', 'show');
            $container['route']->addRoute('c/:project_id', 'calendar', 'show');

            // Listing routes
            $container['route']->addRoute('list/:project_id', 'listing', 'show');
            $container['route']->addRoute('l/:project_id', 'listing', 'show');

            // Gantt routes
            $container['route']->addRoute('gantt/:project_id', 'gantt', 'project');
            $container['route']->addRoute('gantt/:project_id/sort/:sorting', 'gantt', 'project');

            // Feed routes
            $container['route']->addRoute('feed/project/:token', 'feed', 'project');
            $container['route']->addRoute('feed/user/:token', 'feed', 'user');

            // Ical routes
            $container['route']->addRoute('ical/project/:token', 'ical', 'project');
            $container['route']->addRoute('ical/user/:token', 'ical', 'user');

            // Users
            $container['route']->addRoute('users', 'UserListController', 'show');
            $container['route']->addRoute('user/profile/:user_id', 'UserViewController', 'profile');
            $container['route']->addRoute('user/show/:user_id', 'UserViewController', 'show');
            $container['route']->addRoute('user/show/:user_id/timesheet', 'UserViewController', 'timesheet');
            $container['route']->addRoute('user/show/:user_id/last-logins', 'UserViewController', 'lastLogin');
            $container['route']->addRoute('user/show/:user_id/sessions', 'UserViewController', 'sessions');
            $container['route']->addRoute('user/:user_id/edit', 'UserModificationController', 'show');
            $container['route']->addRoute('user/:user_id/password', 'UserCredentialController', 'changePassword');
            $container['route']->addRoute('user/:user_id/share', 'UserViewController', 'share');
            $container['route']->addRoute('user/:user_id/notifications', 'UserViewController', 'notifications');
            $container['route']->addRoute('user/:user_id/accounts', 'UserViewController', 'external');
            $container['route']->addRoute('user/:user_id/integrations', 'UserViewController', 'integrations');
            $container['route']->addRoute('user/:user_id/authentication', 'UserCredentialController', 'changeAuthentication');
            $container['route']->addRoute('user/:user_id/2fa', 'twofactor', 'index');
            $container['route']->addRoute('user/:user_id/avatar', 'AvatarFile', 'show');

            // Groups
            $container['route']->addRoute('groups', 'GroupListController', 'index');
            $container['route']->addRoute('group/:group_id/members', 'GroupListController', 'users');

            // Config
            $container['route']->addRoute('settings', 'config', 'index');
            $container['route']->addRoute('settings/application', 'config', 'application');
            $container['route']->addRoute('settings/project', 'config', 'project');
            $container['route']->addRoute('settings/project', 'config', 'project');
            $container['route']->addRoute('settings/board', 'config', 'board');
            $container['route']->addRoute('settings/calendar', 'config', 'calendar');
            $container['route']->addRoute('settings/integrations', 'config', 'integrations');
            $container['route']->addRoute('settings/webhook', 'config', 'webhook');
            $container['route']->addRoute('settings/api', 'config', 'api');
            $container['route']->addRoute('settings/links', 'link', 'index');
            $container['route']->addRoute('settings/currencies', 'currency', 'index');

            // Plugins
            $container['route']->addRoute('extensions', 'PluginController', 'show');
            $container['route']->addRoute('extensions/list', 'PluginController', 'directory');

            // Doc
            $container['route']->addRoute('documentation/:file', 'doc', 'show');
            $container['route']->addRoute('documentation', 'doc', 'show');

            // Auth routes
            $container['route']->addRoute('login', 'auth', 'login');
            $container['route']->addRoute('logout', 'auth', 'logout');

            // PasswordReset
            $container['route']->addRoute('forgot-password', 'PasswordReset', 'create');
            $container['route']->addRoute('forgot-password/change/:token', 'PasswordReset', 'change');
        }

        return $container;
    }
}
