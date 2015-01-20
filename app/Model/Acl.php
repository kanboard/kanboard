<?php

namespace Model;

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
        'user' => array('login', 'check', 'google', 'github'),
        'task' => array('readonly'),
        'board' => array('readonly'),
        'project' => array('feed'),
        'webhook' => '*',
    );

    /**
     * Controllers and actions for project members
     *
     * @access private
     * @var array
     */
    private $member_acl = array(
        'board' => '*',
        'comment' => '*',
        'file' => '*',
        'project' => array('show', 'tasks', 'search', 'activity'),
        'subtask' => '*',
        'task' => '*',
        'calendar' => '*',
    );

    /**
     * Controllers and actions for project managers
     *
     * @access private
     * @var array
     */
    private $manager_acl = array(
        'action' => '*',
        'analytic' => '*',
        'board' => array('movecolumn', 'edit', 'update', 'add', 'remove'),
        'category' => '*',
        'export' => array('tasks', 'subtasks', 'summary'),
        'project' => array('edit', 'update', 'share', 'integration', 'users', 'alloweverybody', 'allow', 'setowner', 'revoke', 'duplicate', 'disable', 'enable'),
        'swimlane' => '*',
    );

    /**
     * Controllers and actions for admins
     *
     * @access private
     * @var array
     */
    private $admin_acl = array(
        'user' => array('index', 'create', 'save', 'remove'),
        'config' => '*',
        'project' => array('remove'),
    );

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
    public function isManagerAction($controller, $action)
    {
        return $this->matchAcl($this->manager_acl, $controller, $action);
    }

    /**
     * Return true if the given action is for project members
     *
     * @access public
     * @param  string   $controller   Controller name
     * @param  string   $action       Action name
     * @return bool
     */
    public function isMemberAction($controller, $action)
    {
        return $this->matchAcl($this->member_acl, $controller, $action);
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

        // Check project manager permissions
        if ($this->isManagerAction($controller, $action)) {
            return $this->isManagerActionAllowed($project_id);
        }

        // Check project member permissions
        if ($this->isMemberAction($controller, $action)) {
            return $project_id > 0 && $this->projectPermission->isMember($project_id, $this->userSession->getId());
        }

        // Other applications actions are allowed
        return true;
    }

    public function isManagerActionAllowed($project_id)
    {
        if ($this->userSession->isAdmin()) {
            return true;
        }

        return $project_id > 0 && $this->projectPermission->isManager($project_id, $this->userSession->getId());
    }
}
