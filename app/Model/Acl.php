<?php

namespace Kanboard\Model;

/**
 * Access List
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Acl extends Base
{
    /**
     * Controllers and actions allowed from outside
     *
     * @access private
     * @var array
     */
    private $public_acl = array(
        'auth' => array('login', 'check', 'captcha'),
        'task' => array('readonly'),
        'board' => array('readonly'),
        'webhook' => '*',
        'ical' => '*',
        'feed' => '*',
        'oauth' => array('google', 'github', 'gitlab'),
    );

    /**
     * Controllers and actions for project members
     *
     * @access private
     * @var array
     */
    private $project_member_acl = array(
        'board' => '*',
        'comment' => '*',
        'file' => '*',
        'project' => array('show'),
        'listing' => '*',
        'activity' => '*',
        'subtask' => '*',
        'task' => '*',
        'taskduplication' => '*',
        'taskcreation' => '*',
        'taskmodification' => '*',
        'taskstatus' => '*',
        'tasklink' => '*',
        'timer' => '*',
        'customfilter' => '*',
        'calendar' => array('show', 'project'),
    );

    /**
     * Controllers and actions for project managers
     *
     * @access private
     * @var array
     */
    private $project_manager_acl = array(
        'action' => '*',
        'analytic' => '*',
        'category' => '*',
        'column' => '*',
        'export' => '*',
        'taskimport' => '*',
        'project' => array('edit', 'update', 'share', 'integrations', 'notifications', 'users', 'alloweverybody', 'allow', 'setowner', 'revoke', 'duplicate', 'disable', 'enable'),
        'swimlane' => '*',
        'gantt' => array('project', 'savetaskdate', 'task', 'savetask'),
    );

    /**
     * Controllers and actions for project admins
     *
     * @access private
     * @var array
     */
    private $project_admin_acl = array(
        'project' => array('remove'),
        'projectuser' => '*',
        'gantt' => array('projects', 'saveprojectdate'),
    );

    /**
     * Controllers and actions for admins
     *
     * @access private
     * @var array
     */
    private $admin_acl = array(
        'user' => array('index', 'create', 'save', 'remove', 'authentication'),
        'userimport' => '*',
        'config' => '*',
        'link' => '*',
        'currency' => '*',
        'twofactor' => array('disable'),
    );

    /**
     * Extend ACL rules
     *
     * @access public
     * @param string $acl_name
     * @param aray   $rules
     */
    public function extend($acl_name, array $rules)
    {
        $this->$acl_name = array_merge($this->$acl_name, $rules);
    }

    /**
     * Return true if the specified controller/action match the given acl
     *
     * @access public
     * @param  array    $acl          Acl list
     * @param  string   $controller   Controller name
     * @param  string   $action       Action name
     * @return bool
     */
    public function matchAcl(array $acl, $controller, $action)
    {
        $controller = strtolower($controller);
        $action = strtolower($action);
        return isset($acl[$controller]) && $this->hasAction($action, $acl[$controller]);
    }

    /**
     * Return true if the specified action is inside the list of actions
     *
     * @access public
     * @param  string   $action       Action name
     * @param  mixed    $action       Actions list
     * @return bool
     */
    public function hasAction($action, $actions)
    {
        if (is_array($actions)) {
            return in_array($action, $actions);
        }

        return $actions === '*';
    }

    /**
     * Return true if the given action is public
     *
     * @access public
     * @param  string   $controller   Controller name
     * @param  string   $action       Action name
     * @return bool
     */
    public function isPublicAction($controller, $action)
    {
        return $this->matchAcl($this->public_acl, $controller, $action);
    }

    /**
     * Return true if the given action is for admins
     *
     * @access public
     * @param  string   $controller   Controller name
     * @param  string   $action       Action name
     * @return bool
     */
    public function isAdminAction($controller, $action)
    {
        return $this->matchAcl($this->admin_acl, $controller, $action);
    }

    /**
     * Return true if the given action is for project managers
     *
     * @access public
     * @param  string   $controller   Controller name
     * @param  string   $action       Action name
     * @return bool
     */
    public function isProjectManagerAction($controller, $action)
    {
        return $this->matchAcl($this->project_manager_acl, $controller, $action);
    }

    /**
     * Return true if the given action is for application managers
     *
     * @access public
     * @param  string   $controller   Controller name
     * @param  string   $action       Action name
     * @return bool
     */
    public function isProjectAdminAction($controller, $action)
    {
        return $this->matchAcl($this->project_admin_acl, $controller, $action);
    }

    /**
     * Return true if the given action is for project members
     *
     * @access public
     * @param  string   $controller   Controller name
     * @param  string   $action       Action name
     * @return bool
     */
    public function isProjectMemberAction($controller, $action)
    {
        return $this->matchAcl($this->project_member_acl, $controller, $action);
    }

    /**
     * Return true if the visitor is allowed to access to the given page
     * We suppose the user already authenticated
     *
     * @access public
     * @param  string   $controller   Controller name
     * @param  string   $action       Action name
     * @param  integer  $project_id   Project id
     * @return bool
     */
    public function isAllowed($controller, $action, $project_id = 0)
    {
        // If you are admin you have access to everything
        if ($this->userSession->isAdmin()) {
            return true;
        }

        // If you access to an admin action, your are not allowed
        if ($this->isAdminAction($controller, $action)) {
            return false;
        }

        // Check project admin permissions
        if ($this->isProjectAdminAction($controller, $action)) {
            return $this->handleProjectAdminPermissions($project_id);
        }

        // Check project manager permissions
        if ($this->isProjectManagerAction($controller, $action)) {
            return $this->handleProjectManagerPermissions($project_id);
        }

        // Check project member permissions
        if ($this->isProjectMemberAction($controller, $action)) {
            return $project_id > 0 && $this->projectPermission->isMember($project_id, $this->userSession->getId());
        }

        // Other applications actions are allowed
        return true;
    }

    /**
     * Handle permission for project manager
     *
     * @access public
     * @param integer $project_id
     * @return boolean
     */
    public function handleProjectManagerPermissions($project_id)
    {
        if ($project_id > 0) {
            if ($this->userSession->isProjectAdmin()) {
                return $this->projectPermission->isMember($project_id, $this->userSession->getId());
            }

            return $this->projectPermission->isManager($project_id, $this->userSession->getId());
        }

        return false;
    }

    /**
     * Handle permission for project admins
     *
     * @access public
     * @param integer $project_id
     * @return boolean
     */
    public function handleProjectAdminPermissions($project_id)
    {
        if (! $this->userSession->isProjectAdmin()) {
            return false;
        }

        if ($project_id > 0) {
            return $this->projectPermission->isMember($project_id, $this->userSession->getId());
        }

        return true;
    }
}
