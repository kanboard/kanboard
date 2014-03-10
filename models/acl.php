<?php

namespace Model;

require_once __DIR__.'/base.php';

class Acl extends Base
{
    // Controllers and actions allowed from outside
    private $public_actions = array(
        'user' => array('login', 'check'),
        'task' => array('add'),
        'board' => array('readonly'),
    );

    // Controllers and actions allowed for regular users
    private $user_actions = array(
        'app' => array('index'),
        'board' => array('index', 'show', 'assign', 'assigntask', 'save'),
        'project' => array('tasks', 'index', 'forbidden'),
        'task' => array('show', 'create', 'save', 'edit', 'update', 'close', 'confirmclose', 'open', 'confirmopen', 'comment', 'description'),
        'user' => array('index', 'edit', 'update', 'forbidden', 'logout', 'index'),
        'config' => array('index'),
    );

    // Return true if the specified controller/action is allowed according to the given acl
    public function isAllowedAction(array $acl, $controller, $action)
    {
        if (isset($acl[$controller])) {
            return in_array($action, $acl[$controller]);
        }

        return false;
    }

    // Return true if the given action is public
    public function isPublicAction($controller, $action)
    {
        return $this->isAllowedAction($this->public_actions, $controller, $action);
    }

    // Return true if the given action is allowed for a regular user
    public function isUserAction($controller, $action)
    {
        return $this->isAllowedAction($this->user_actions, $controller, $action);
    }

    // Return true if the logged user is admin
    public function isAdminUser()
    {
        return isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] === '1';
    }

    // Return true if the logged user is not admin
    public function isRegularUser()
    {
        return isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] === '0';
    }

    // Get the connected user id
    public function getUserId()
    {
        return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : 0;
    }

    // Check if an action is allowed for the logged user
    public function isPageAccessAllowed($controller, $action)
    {
        return $this->isPublicAction($controller, $action) ||
               $this->isAdminUser() ||
               ($this->isRegularUser() && $this->isUserAction($controller, $action));
    }
}
