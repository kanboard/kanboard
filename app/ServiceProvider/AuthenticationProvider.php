<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\Security\AuthenticationManager;
use Kanboard\Core\Security\AccessMap;
use Kanboard\Core\Security\Authorization;
use Kanboard\Core\Security\Role;
use Kanboard\Auth\RememberMeAuth;
use Kanboard\Auth\DatabaseAuth;
use Kanboard\Auth\LdapAuth;
use Kanboard\Auth\TotpAuth;
use Kanboard\Auth\ReverseProxyAuth;

/**
 * Authentication Provider
 *
 * @package serviceProvider
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

        if (LDAP_AUTH) {
            $container['authenticationManager']->register(new LdapAuth($container));
        }

        $container['projectAccessMap'] = $this->getProjectAccessMap();
        $container['applicationAccessMap'] = $this->getApplicationAccessMap();

        $container['projectAuthorization'] = new Authorization($container['projectAccessMap']);
        $container['applicationAuthorization'] = new Authorization($container['applicationAccessMap']);

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

        $acl->add('Action', '*', Role::PROJECT_MANAGER);
        $acl->add('ActionProject', '*', Role::PROJECT_MANAGER);
        $acl->add('ActionCreation', '*', Role::PROJECT_MANAGER);
        $acl->add('Analytic', '*', Role::PROJECT_MANAGER);
        $acl->add('Board', 'save', Role::PROJECT_MEMBER);
        $acl->add('BoardPopover', '*', Role::PROJECT_MEMBER);
        $acl->add('Calendar', 'save', Role::PROJECT_MEMBER);
        $acl->add('Category', '*', Role::PROJECT_MANAGER);
        $acl->add('Column', '*', Role::PROJECT_MANAGER);
        $acl->add('Comment', '*', Role::PROJECT_MEMBER);
        $acl->add('Customfilter', '*', Role::PROJECT_MEMBER);
        $acl->add('Export', '*', Role::PROJECT_MANAGER);
        $acl->add('TaskFile', array('screenshot', 'create', 'save', 'remove', 'confirm'), Role::PROJECT_MEMBER);
        $acl->add('Gantt', '*', Role::PROJECT_MANAGER);
        $acl->add('Project', array('share', 'integrations', 'notifications', 'duplicate', 'disable', 'enable', 'remove'), Role::PROJECT_MANAGER);
        $acl->add('ProjectPermission', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectEdit', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectFile', '*', Role::PROJECT_MEMBER);
        $acl->add('Projectuser', '*', Role::PROJECT_MANAGER);
        $acl->add('Subtask', '*', Role::PROJECT_MEMBER);
        $acl->add('SubtaskRestriction', '*', Role::PROJECT_MEMBER);
        $acl->add('SubtaskStatus', '*', Role::PROJECT_MEMBER);
        $acl->add('Swimlane', '*', Role::PROJECT_MANAGER);
        $acl->add('Task', 'remove', Role::PROJECT_MEMBER);
        $acl->add('Taskcreation', '*', Role::PROJECT_MEMBER);
        $acl->add('Taskduplication', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskRecurrence', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskImport', '*', Role::PROJECT_MANAGER);
        $acl->add('TaskInternalLink', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskExternalLink', '*', Role::PROJECT_MEMBER);
        $acl->add('Taskmodification', '*', Role::PROJECT_MEMBER);
        $acl->add('Taskstatus', '*', Role::PROJECT_MEMBER);
        $acl->add('UserHelper', array('mention'), Role::PROJECT_MEMBER);

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

        $acl->add('Auth', array('login', 'check'), Role::APP_PUBLIC);
        $acl->add('Captcha', '*', Role::APP_PUBLIC);
        $acl->add('PasswordReset', '*', Role::APP_PUBLIC);
        $acl->add('Webhook', '*', Role::APP_PUBLIC);
        $acl->add('Task', 'readonly', Role::APP_PUBLIC);
        $acl->add('Board', 'readonly', Role::APP_PUBLIC);
        $acl->add('Ical', '*', Role::APP_PUBLIC);
        $acl->add('Feed', '*', Role::APP_PUBLIC);
        $acl->add('AvatarFile', 'show', Role::APP_PUBLIC);

        $acl->add('Config', '*', Role::APP_ADMIN);
        $acl->add('Currency', '*', Role::APP_ADMIN);
        $acl->add('Gantt', array('projects', 'saveProjectDate'), Role::APP_MANAGER);
        $acl->add('Group', '*', Role::APP_ADMIN);
        $acl->add('Link', '*', Role::APP_ADMIN);
        $acl->add('ProjectCreation', 'create', Role::APP_MANAGER);
        $acl->add('Projectuser', '*', Role::APP_MANAGER);
        $acl->add('Twofactor', 'disable', Role::APP_ADMIN);
        $acl->add('UserImport', '*', Role::APP_ADMIN);
        $acl->add('User', array('index', 'create', 'save', 'authentication'), Role::APP_ADMIN);
        $acl->add('UserStatus', '*', Role::APP_ADMIN);

        return $acl;
    }
}
