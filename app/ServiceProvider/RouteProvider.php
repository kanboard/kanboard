<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\Http\Route;
use Kanboard\Core\Http\Router;

/**
 * Route Provider
 *
 * @package Kanboard\ServiceProvider
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
            $container['route']->addRoute('dashboard/:user_id/activity', 'DashboardController', 'activity');
            $container['route']->addRoute('dashboard/:user_id/notifications', 'DashboardController', 'notifications');
            $container['route']->addRoute('my-activity', 'ActivityController', 'user');

            // Search routes
            $container['route']->addRoute('search', 'SearchController', 'index');
            $container['route']->addRoute('search/activity', 'SearchController', 'activity');

            // ProjectCreation routes
            $container['route']->addRoute('project/create', 'ProjectCreationController', 'create');
            $container['route']->addRoute('project/create/personal', 'ProjectCreationController', 'createPrivate');

            // Project routes
            $container['route']->addRoute('projects', 'ProjectListController', 'show');
            $container['route']->addRoute('project/:project_id', 'ProjectViewController', 'show');
            $container['route']->addRoute('p/:project_id', 'ProjectViewController', 'show');
            $container['route']->addRoute('project/:project_id/customer-filters', 'CustomFilterController', 'index');
            $container['route']->addRoute('project/:project_id/customer-filters/create', 'CustomFilterController', 'create');
            $container['route']->addRoute('project/:project_id/share', 'ProjectViewController', 'share');
            $container['route']->addRoute('project/:project_id/notifications', 'ProjectViewController', 'notifications');
            $container['route']->addRoute('project/:project_id/integrations', 'ProjectViewController', 'integrations');
            $container['route']->addRoute('project/:project_id/duplicate', 'ProjectViewController', 'duplicate');
            $container['route']->addRoute('project/:project_id/permissions', 'ProjectPermissionController', 'index');
            $container['route']->addRoute('project/:project_id/activity', 'ActivityController', 'project');
            $container['route']->addRoute('project/:project_id/tags', 'ProjectTagController', 'index');
            $container['route']->addRoute('project/:project_id/task/create', 'TaskCreationController', 'show');
            $container['route']->addRoute('project/:project_id/predefined-contents', 'ProjectPredefinedContentController', 'show');
            $container['route']->addRoute('project/:project_id/predefined-contents/create', 'PredefinedTaskDescriptionController', 'create');
            $container['route']->addRoute('project/:project_id/predefined-contents/save', 'PredefinedTaskDescriptionController', 'save');
            $container['route']->addRoute('project/:project_id/predefined-contents/edit/:id', 'PredefinedTaskDescriptionController', 'edit');
            $container['route']->addRoute('project/:project_id/predefined-contents/remove/:id', 'PredefinedTaskDescriptionController', 'confirm');
            $container['route']->addRoute('project/:project_id/custom-roles', 'ProjectRoleController', 'show');
            $container['route']->addRoute('project/:project_id/import/tasks', 'ProjectViewController', 'importTasks');
            $container['route']->addRoute('project/:project_id/enable', 'ProjectStatusController', 'confirmEnable');
            $container['route']->addRoute('project/:project_id/disable', 'ProjectStatusController', 'confirmDisable');
            $container['route']->addRoute('project/:project_id/remove', 'ProjectStatusController', 'confirmRemove');

            // Project Overview
            $container['route']->addRoute('project/:project_id/overview', 'ProjectOverviewController', 'show');
            $container['route']->addRoute('project/:project_id/overview/:search', 'ProjectOverviewController', 'show');

            // ProjectEdit routes
            $container['route']->addRoute('project/:project_id/edit', 'ProjectEditController', 'show');

            // ProjectUser routes
            $container['route']->addRoute('projects/managers/:user_id', 'ProjectUserOverviewController', 'managers');
            $container['route']->addRoute('projects/members/:user_id', 'ProjectUserOverviewController', 'members');
            $container['route']->addRoute('projects/tasks/:user_id/opens', 'ProjectUserOverviewController', 'opens');
            $container['route']->addRoute('projects/tasks/:user_id/closed', 'ProjectUserOverviewController', 'closed');
            $container['route']->addRoute('projects/managers', 'ProjectUserOverviewController', 'managers');

            // Action routes
            $container['route']->addRoute('project/:project_id/actions', 'ActionController', 'index');
            $container['route']->addRoute('project/:project_id/action/:action_id/confirm', 'ActionController', 'confirm');
            $container['route']->addRoute('project/:project_id/action/:action_id/remove', 'ActionController', 'remove');
            $container['route']->addRoute('project/:project_id/action/create', 'ActionCreationController', 'create');
            $container['route']->addRoute('project/:project_id/action/event', 'ActionCreationController', 'event');
            $container['route']->addRoute('project/:project_id/action/params', 'ActionCreationController', 'params');
            $container['route']->addRoute('project/:project_id/action/save', 'ActionCreationController', 'save');

            // Column routes
            $container['route']->addRoute('project/:project_id/columns', 'ColumnController', 'index');

            // Swimlane routes
            $container['route']->addRoute('project/:project_id/swimlanes', 'SwimlaneController', 'index');

            // Category routes
            $container['route']->addRoute('project/:project_id/categories', 'CategoryController', 'index');

            // Import routes
            $container['route']->addRoute('project/:project_id/import', 'TaskImportController', 'show');

            // Task routes
            $container['route']->addRoute('task/:task_id', 'TaskViewController', 'show');
            $container['route']->addRoute('t/:task_id', 'TaskViewController', 'show');
            $container['route']->addRoute('public/task/:task_id/:token', 'TaskViewController', 'readonly');

            $container['route']->addRoute('task/:task_id/activity', 'ActivityController', 'task');
            $container['route']->addRoute('task/:task_id/transitions', 'TaskViewController', 'transitions');
            $container['route']->addRoute('task/:task_id/analytics', 'TaskViewController', 'analytics');
            $container['route']->addRoute('task/:task_id/time-tracking', 'TaskViewController', 'timetracking');
            $container['route']->addRoute('task/:task_id/position/show', 'TaskMovePositionController', 'show');
            $container['route']->addRoute('task/:task_id/position/save', 'TaskMovePositionController', 'save');
            $container['route']->addRoute('task/:task_id/edit', 'TaskModificationController', 'edit');
            $container['route']->addRoute('task/:task_id/update', 'TaskModificationController', 'update');
            $container['route']->addRoute('task/:task_id/assign-to-me/:csrf_token', 'TaskModificationController', 'assignToMe');
            $container['route']->addRoute('task/:task_id/start/:csrf_token', 'TaskModificationController', 'start');
            $container['route']->addRoute('task/:task_id/assign-to-me/redirect/:redirect/:csrf_token', 'TaskModificationController', 'assignToMe');
            $container['route']->addRoute('task/:task_id/start/redirect/:redirect/:csrf_token', 'TaskModificationController', 'start');
            $container['route']->addRoute('task/:task_id/close', 'TaskStatusController', 'close');
            $container['route']->addRoute('task/:task_id/open', 'TaskStatusController', 'open');
            $container['route']->addRoute('task/:task_id/email/create', 'TaskMailController', 'create');
            $container['route']->addRoute('task/:task_id/email/send', 'TaskMailController', 'send');
            $container['route']->addRoute('task/:task_id/duplicate', 'TaskDuplicationController', 'duplicate');
            $container['route']->addRoute('task/:task_id/move-to-project/:project_id', 'TaskDuplicationController', 'move');
            $container['route']->addRoute('task/:task_id/copy-to-project/:project_id', 'TaskDuplicationController', 'copy');
            $container['route']->addRoute('task/:task_id/screenshot', 'TaskPopoverController', 'screenshot');
            $container['route']->addRoute('task/:task_id/file/screenshot', 'TaskFileController', 'screenshot');
            $container['route']->addRoute('task/:task_id/file/create', 'TaskFileController', 'create');
            $container['route']->addRoute('task/:task_id/file/save', 'TaskFileController', 'save');
            $container['route']->addRoute('task/:task_id/file/remove', 'TaskFileController', 'remove');
            $container['route']->addRoute('task/:task_id/external-link/find', 'TaskExternalLinkController', 'find');
            $container['route']->addRoute('task/:task_id/external-link/create', 'TaskExternalLinkController', 'create');
            $container['route']->addRoute('task/:task_id/external-link/save', 'TaskExternalLinkController', 'save');
            $container['route']->addRoute('task/:task_id/internal-link/create', 'TaskInternalLinkController', 'create');
            $container['route']->addRoute('task/:task_id/internal-link/save', 'TaskInternalLinkController', 'save');
            $container['route']->addRoute('task/:task_id/comment/create', 'CommentController', 'create');
            $container['route']->addRoute('task/:task_id/comment/save', 'CommentController', 'save');
            $container['route']->addRoute('task/:task_id/comment/:comment_id/edit', 'CommentController', 'edit');
            $container['route']->addRoute('task/:task_id/comment/:comment_id/update', 'CommentController', 'update');
            $container['route']->addRoute('task/:task_id/comment/:comment_id/confirm', 'CommentController', 'confirm');
            $container['route']->addRoute('task/:task_id/comment/:comment_id/remove/:csrf_token', 'CommentController', 'remove');
            $container['route']->addRoute('task/:task_id/subtask/create', 'SubtaskController', 'create');
            $container['route']->addRoute('task/:task_id/subtask/save', 'SubtaskController', 'save');
            $container['route']->addRoute('task/:task_id/recurrence/edit', 'TaskRecurrenceController', 'edit');
            $container['route']->addRoute('task/:task_id/remove', 'TaskSuppressionController', 'confirm');
            $container['route']->addRoute('task/:task_id/remove/redirect/:redirect', 'TaskSuppressionController', 'confirm');

            // Exports
            $container['route']->addRoute('export/tasks/:project_id', 'ExportController', 'tasks');
            $container['route']->addRoute('export/subtasks/:project_id', 'ExportController', 'subtasks');
            $container['route']->addRoute('export/transitions/:project_id', 'ExportController', 'transitions');
            $container['route']->addRoute('export/summary/:project_id', 'ExportController', 'summary');

            // Analytics routes
            $container['route']->addRoute('analytics/tasks/:project_id', 'AnalyticController', 'taskDistribution');
            $container['route']->addRoute('analytics/users/:project_id', 'AnalyticController', 'userDistribution');
            $container['route']->addRoute('analytics/cfd/:project_id', 'AnalyticController', 'cfd');
            $container['route']->addRoute('analytics/burndown/:project_id', 'AnalyticController', 'burndown');
            $container['route']->addRoute('analytics/average-time-column/:project_id', 'AnalyticController', 'averageTimeByColumn');
            $container['route']->addRoute('analytics/lead-cycle-time/:project_id', 'AnalyticController', 'leadAndCycleTime');
            $container['route']->addRoute('analytics/estimated-spent-time/:project_id', 'AnalyticController', 'compareHours');

            // Board routes
            $container['route']->addRoute('board/:project_id', 'BoardViewController', 'show');
            $container['route']->addRoute('board/:project_id/search/:search', 'BoardViewController', 'show');
            $container['route']->addRoute('board/:project_id/task/create/swimlane/:swimlane_id/column/:column_id', 'TaskCreationController', 'show');
            $container['route']->addRoute('board/:project_id/task/bulk/create/swimlane/:swimlane_id/column/:column_id', 'TaskBulkController', 'show');
            $container['route']->addRoute('board/:project_id/close-tasks/swimlane/:swimlane_id/column/:column_id', 'BoardPopoverController', 'confirmCloseColumnTasks');
            $container['route']->addRoute('b/:project_id', 'BoardViewController', 'show');
            $container['route']->addRoute('public/board/:token', 'BoardViewController', 'readonly');

            // Listing routes
            $container['route']->addRoute('list/:project_id', 'TaskListController', 'show');
            $container['route']->addRoute('list/:project_id/search/:search', 'TaskListController', 'show');
            $container['route']->addRoute('l/:project_id', 'TaskListController', 'show');

            // Feed routes
            $container['route']->addRoute('feed/project/:token', 'FeedController', 'project');
            $container['route']->addRoute('feed/user/:token', 'FeedController', 'user');

            // Ical routes
            $container['route']->addRoute('ical/project/:token', 'ICalendarController', 'project');
            $container['route']->addRoute('ical/user/:token', 'ICalendarController', 'user');

            // Users
            $container['route']->addRoute('users', 'UserListController', 'show');
            $container['route']->addRoute('user/profile/:user_id', 'UserViewController', 'profile');
            $container['route']->addRoute('user/show/:user_id', 'UserViewController', 'show');
            $container['route']->addRoute('user/show/:user_id/timesheet', 'UserViewController', 'timesheet');
            $container['route']->addRoute('user/show/:user_id/last-logins', 'UserViewController', 'lastLogin');
            $container['route']->addRoute('user/show/:user_id/sessions', 'UserViewController', 'sessions');
            $container['route']->addRoute('user/show/:user_id/password-reset-history', 'UserViewController', 'password');
            $container['route']->addRoute('user/:user_id/edit', 'UserModificationController', 'show');
            $container['route']->addRoute('user/:user_id/password', 'UserCredentialController', 'changePassword');
            $container['route']->addRoute('user/:user_id/share', 'UserViewController', 'share');
            $container['route']->addRoute('user/:user_id/notifications', 'UserViewController', 'notifications');
            $container['route']->addRoute('user/:user_id/accounts', 'UserViewController', 'external');
            $container['route']->addRoute('user/:user_id/integrations', 'UserViewController', 'integrations');
            $container['route']->addRoute('user/:user_id/authentication', 'UserCredentialController', 'changeAuthentication');
            $container['route']->addRoute('user/:user_id/2fa', 'TwoFactorController', 'index');
            $container['route']->addRoute('user/:user_id/avatar', 'AvatarFileController', 'show');
            $container['route']->addRoute('user/:user_id/api', 'UserApiAccessController', 'show');
            $container['route']->addRoute('user/:user_id/notifications/web', 'WebNotificationController', 'show');
            $container['route']->addRoute('user/:user_id/notifications/web/flush/:csrf_token', 'WebNotificationController', 'flush');
            $container['route']->addRoute('user/:user_id/notifications/web/remove/:notification_id/:csrf_token', 'WebNotificationController', 'remove');

            // Groups
            $container['route']->addRoute('groups', 'GroupListController', 'index');
            $container['route']->addRoute('group/:group_id/members', 'GroupListController', 'users');

            // Config
            $container['route']->addRoute('settings', 'ConfigController', 'index');
            $container['route']->addRoute('settings/application', 'ConfigController', 'application');
            $container['route']->addRoute('settings/email', 'ConfigController', 'email');
            $container['route']->addRoute('settings/project', 'ConfigController', 'project');
            $container['route']->addRoute('settings/project', 'ConfigController', 'project');
            $container['route']->addRoute('settings/board', 'ConfigController', 'board');
            $container['route']->addRoute('settings/integrations', 'ConfigController', 'integrations');
            $container['route']->addRoute('settings/webhook', 'ConfigController', 'webhook');
            $container['route']->addRoute('settings/api', 'ConfigController', 'api');
            $container['route']->addRoute('settings/links', 'LinkController', 'index');
            $container['route']->addRoute('settings/currencies', 'CurrencyController', 'show');
            $container['route']->addRoute('settings/currencies/create', 'CurrencyController', 'create');
            $container['route']->addRoute('settings/currencies/change', 'CurrencyController', 'change');
            $container['route']->addRoute('settings/tags', 'TagController', 'index');
            $container['route']->addRoute('settings/links/labels', 'LinkController', 'show');
            $container['route']->addRoute('settings/links/labels/create', 'LinkController', 'create');
            $container['route']->addRoute('settings/links/labels/edit/:link_id', 'LinkController', 'edit');
            $container['route']->addRoute('settings/links/labels/update/:link_id', 'LinkController', 'update');
            $container['route']->addRoute('settings/links/labels/confirm/:link_id', 'LinkController', 'confirm');
            $container['route']->addRoute('settings/links/labels/remove/:link_id/:csrf_token', 'LinkController', 'remove');

            // Plugins
            $container['route']->addRoute('extensions', 'PluginController', 'show');
            $container['route']->addRoute('extensions/directory', 'PluginController', 'directory');

            // Doc
            $container['route']->addRoute('documentation/:file', 'DocumentationController', 'show');
            $container['route']->addRoute('documentation', 'DocumentationController', 'show');

            // Auth routes
            $container['route']->addRoute('login', 'AuthController', 'login');
            $container['route']->addRoute('logout', 'AuthController', 'logout');

            // PasswordReset
            $container['route']->addRoute('forgot-password', 'PasswordResetController', 'create');
            $container['route']->addRoute('forgot-password/change/:token', 'PasswordResetController', 'change');

            // Cronjob
            $container['route']->addRoute('cronjob', 'CronjobController', 'run');
        }

        return $container;
    }
}
