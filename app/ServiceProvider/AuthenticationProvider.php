<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\Security\AuthenticationManager;
use Kanboard\Core\Security\AccessMap;
use Kanboard\Core\Security\Authorization;
use Kanboard\Core\Security\Role;
use Kanboard\Auth\ApiAccessTokenAuth;
use Kanboard\Auth\RememberMeAuth;
use Kanboard\Auth\DatabaseAuth;
use Kanboard\Auth\LdapAuth;
use Kanboard\Auth\TotpAuth;
use Kanboard\Auth\ReverseProxyAuth;

/**
 * Authentication Provider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class AuthenticationProvider implements ServiceProviderInterface
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
        $container['authenticationManager'] = new AuthenticationManager($container);
        $container['authenticationManager']->register(new TotpAuth($container));
        $container['authenticationManager']->register(new RememberMeAuth($container));
        $container['authenticationManager']->register(new DatabaseAuth($container));

        if (REVERSE_PROXY_AUTH) {
            $container['authenticationManager']->register(new ReverseProxyAuth($container));
        }

        $container['authenticationManager']->register(new ApiAccessTokenAuth($container));
        
        if (LDAP_AUTH) {
            $container['authenticationManager']->register(new LdapAuth($container));
        }     

        $container['projectAccessMap'] = $this->getProjectAccessMap();
        $container['applicationAccessMap'] = $this->getApplicationAccessMap();
        $container['apiAccessMap'] = $this->getApiAccessMap();
        $container['apiProjectAccessMap'] = $this->getApiProjectAccessMap();

        $container['projectAuthorization'] = new Authorization($container['projectAccessMap']);
        $container['applicationAuthorization'] = new Authorization($container['applicationAccessMap']);
        $container['apiAuthorization'] = new Authorization($container['apiAccessMap']);
        $container['apiProjectAuthorization'] = new Authorization($container['apiProjectAccessMap']);

        return $container;
    }

    /**
     * Get ACL for projects
     *
     * @access public
     * @return AccessMap
     */
    public function getProjectAccessMap()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole(Role::PROJECT_VIEWER);
        $acl->setRoleHierarchy(Role::PROJECT_MANAGER, array(Role::PROJECT_MEMBER, Role::PROJECT_VIEWER));
        $acl->setRoleHierarchy(Role::PROJECT_MEMBER, array(Role::PROJECT_VIEWER));

        $acl->add('ActionController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectActionDuplicationController', '*', Role::PROJECT_MANAGER);
        $acl->add('ActionCreationController', '*', Role::PROJECT_MANAGER);
        $acl->add('AnalyticController', '*', Role::PROJECT_MANAGER);
        $acl->add('BoardAjaxController', 'save', Role::PROJECT_MEMBER);
        $acl->add('BoardPopoverController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskPopoverController', '*', Role::PROJECT_MEMBER);
        $acl->add('CalendarController', 'save', Role::PROJECT_MEMBER);
        $acl->add('CategoryController', '*', Role::PROJECT_MANAGER);
        $acl->add('ColumnController', '*', Role::PROJECT_MANAGER);
        $acl->add('CommentController', array('create', 'save', 'edit', 'update', 'confirm', 'remove'), Role::PROJECT_MEMBER);
        $acl->add('CommentListController', array('save'), Role::PROJECT_MEMBER);
        $acl->add('CommentMailController', '*', Role::PROJECT_MEMBER);
        $acl->add('CustomFilterController', '*', Role::PROJECT_MEMBER);
        $acl->add('ExportController', '*', Role::PROJECT_MANAGER);
        $acl->add('TaskFileController', array('screenshot', 'create', 'save', 'remove', 'confirm'), Role::PROJECT_MEMBER);
        $acl->add('ProjectViewController', array('share', 'updateSharing', 'integrations', 'updateIntegrations', 'notifications', 'updateNotifications', 'duplicate', 'doDuplication'), Role::PROJECT_MANAGER);
        $acl->add('ProjectPermissionController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectEditController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectPredefinedContentController', '*', Role::PROJECT_MANAGER);
        $acl->add('PredefinedTaskDescriptionController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectFileController', '*', Role::PROJECT_MEMBER);
        $acl->add('ProjectUserOverviewController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectStatusController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectTagController', '*', Role::PROJECT_MANAGER);
        $acl->add('SubtaskController', '*', Role::PROJECT_MEMBER);
        $acl->add('SubtaskConverterController', '*', Role::PROJECT_MEMBER);
        $acl->add('SubtaskRestrictionController', '*', Role::PROJECT_MEMBER);
        $acl->add('SubtaskStatusController', '*', Role::PROJECT_MEMBER);
        $acl->add('SwimlaneController', '*', Role::PROJECT_MANAGER);
        $acl->add('TaskSuppressionController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskCreationController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskBulkController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskBulkMoveColumnController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskBulkChangePropertyController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskDuplicationController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskRecurrenceController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskImportController', '*', Role::PROJECT_MANAGER);
        $acl->add('TaskInternalLinkController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskExternalLinkController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskModificationController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskStatusController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskMailController', '*', Role::PROJECT_MEMBER);
        $acl->add('UserAjaxController', array('mention'), Role::PROJECT_MEMBER);

        return $acl;
    }

    /**
     * Get ACL for the application
     *
     * @access public
     * @return AccessMap
     */
    public function getApplicationAccessMap()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole(Role::APP_USER);
        $acl->setRoleHierarchy(Role::APP_ADMIN, array(Role::APP_MANAGER, Role::APP_USER, Role::APP_PUBLIC));
        $acl->setRoleHierarchy(Role::APP_MANAGER, array(Role::APP_USER, Role::APP_PUBLIC));
        $acl->setRoleHierarchy(Role::APP_USER, array(Role::APP_PUBLIC));

        $acl->add('AuthController', array('login', 'check'), Role::APP_PUBLIC);
        $acl->add('CaptchaController', '*', Role::APP_PUBLIC);
        $acl->add('PasswordResetController', '*', Role::APP_PUBLIC);
        $acl->add('TaskViewController', 'readonly', Role::APP_PUBLIC);
        $acl->add('BoardViewController', 'readonly', Role::APP_PUBLIC);
        $acl->add('ICalendarController', '*', Role::APP_PUBLIC);
        $acl->add('FeedController', '*', Role::APP_PUBLIC);
        $acl->add('AvatarFileController', array('show', 'image'), Role::APP_PUBLIC);
        $acl->add('UserInviteController', array('signup', 'register'), Role::APP_PUBLIC);
        $acl->add('CronjobController', array('run'), Role::APP_PUBLIC);

        $acl->add('ConfigController', '*', Role::APP_ADMIN);
        $acl->add('TagController', '*', Role::APP_ADMIN);
        $acl->add('PluginController', '*', Role::APP_ADMIN);
        $acl->add('CurrencyController', '*', Role::APP_ADMIN);
        $acl->add('GroupListController', '*', Role::APP_ADMIN);
        $acl->add('GroupCreationController', '*', Role::APP_ADMIN);
        $acl->add('GroupModificationController', '*', Role::APP_ADMIN);
        $acl->add('LinkController', '*', Role::APP_ADMIN);
        $acl->add('ProjectCreationController', 'create', Role::APP_MANAGER);
        $acl->add('ProjectUserOverviewController', '*', Role::APP_MANAGER);
        $acl->add('TwoFactorController', 'disable', Role::APP_ADMIN);
        $acl->add('UserImportController', '*', Role::APP_ADMIN);
        $acl->add('UserCreationController', '*', Role::APP_ADMIN);
        $acl->add('UserListController', '*', Role::APP_ADMIN);
        $acl->add('UserStatusController', '*', Role::APP_ADMIN);
        $acl->add('UserCredentialController', array('changeAuthentication', 'saveAuthentication', 'unlock'), Role::APP_ADMIN);

        return $acl;
    }

    /**
     * Get ACL for the API
     *
     * @access public
     * @return AccessMap
     */
    public function getApiAccessMap()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole(Role::APP_USER);
        $acl->setRoleHierarchy(Role::APP_ADMIN, array(Role::APP_MANAGER, Role::APP_USER, Role::APP_PUBLIC));
        $acl->setRoleHierarchy(Role::APP_MANAGER, array(Role::APP_USER, Role::APP_PUBLIC));

        $acl->add('UserProcedure', '*', Role::APP_ADMIN);
        $acl->add('GroupMemberProcedure', '*', Role::APP_ADMIN);
        $acl->add('GroupProcedure', '*', Role::APP_ADMIN);
        $acl->add('LinkProcedure', '*', Role::APP_ADMIN);
        $acl->add('TaskProcedure', array('getOverdueTasks'), Role::APP_ADMIN);
        $acl->add('ProjectProcedure', array('getAllProjects'), Role::APP_ADMIN);
        $acl->add('ProjectProcedure', array('createProject'), Role::APP_MANAGER);

        return $acl;
    }

    /**
     * Get ACL for the API
     *
     * @access public
     * @return AccessMap
     */
    public function getApiProjectAccessMap()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole(Role::PROJECT_VIEWER);
        $acl->setRoleHierarchy(Role::PROJECT_MANAGER, array(Role::PROJECT_MEMBER, Role::PROJECT_VIEWER));
        $acl->setRoleHierarchy(Role::PROJECT_MEMBER, array(Role::PROJECT_VIEWER));

        $acl->add('ActionProcedure', array('removeAction', 'getActions', 'createAction'), Role::PROJECT_MANAGER);
        $acl->add('CategoryProcedure', array('removeCategory', 'createCategory', 'updateCategory'), Role::PROJECT_MANAGER);
        $acl->add('ColumnProcedure', array('updateColumn', 'addColumn', 'removeColumn', 'changeColumnPosition'), Role::PROJECT_MANAGER);
        $acl->add('CommentProcedure', array('removeComment', 'createComment', 'updateComment'), Role::PROJECT_MEMBER);
        $acl->add('ProjectPermissionProcedure', array('addProjectUser', 'addProjectGroup', 'removeProjectUser', 'removeProjectGroup', 'changeProjectUserRole', 'changeProjectGroupRole'), Role::PROJECT_MANAGER);
        $acl->add('ProjectProcedure', array('updateProject', 'removeProject', 'enableProject', 'disableProject', 'enableProjectPublicAccess', 'disableProjectPublicAccess'), Role::PROJECT_MANAGER);
        $acl->add('SubtaskProcedure', array('removeSubtask', 'createSubtask', 'updateSubtask'), Role::PROJECT_MEMBER);
        $acl->add('SubtaskTimeTrackingProcedure', array('setSubtaskStartTime', 'setSubtaskEndTime'), Role::PROJECT_MEMBER);
        $acl->add('SwimlaneProcedure', array('addSwimlane', 'updateSwimlane', 'removeSwimlane', 'disableSwimlane', 'enableSwimlane', 'changeSwimlanePosition'), Role::PROJECT_MANAGER);
        $acl->add('ProjectFileProcedure', array('createProjectFile', 'removeProjectFile', 'removeAllProjectFiles'), Role::PROJECT_MEMBER);
        $acl->add('TaskFileProcedure', array('createTaskFile', 'removeTaskFile', 'removeAllTaskFiles'), Role::PROJECT_MEMBER);
        $acl->add('TaskLinkProcedure', array('createTaskLink', 'updateTaskLink', 'removeTaskLink'), Role::PROJECT_MEMBER);
        $acl->add('TaskExternalLinkProcedure', array('createExternalTaskLink', 'updateExternalTaskLink', 'removeExternalTaskLink'), Role::PROJECT_MEMBER);
        $acl->add('TaskProcedure', array('openTask', 'closeTask', 'removeTask', 'moveTaskPosition', 'moveTaskToProject', 'duplicateTaskToProject', 'createTask', 'updateTask'), Role::PROJECT_MEMBER);
        $acl->add('TaskTagProcedure', array('setTaskTags'), Role::PROJECT_MEMBER);
        $acl->add('TagProcedure', array('createTag', 'updateTag', 'removeTag'), Role::PROJECT_MEMBER);

        return $acl;
    }
}
