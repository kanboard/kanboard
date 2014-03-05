<?php

namespace Model;

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
        'task' => array('show', 'create', 'save', 'edit', 'update', 'close', 'confirmclose', 'open', 'confirmopen', 'comment'),
        'user' => array('index', 'edit', 'update', 'forbidden', 'logout', 'index'),
        'config' => array('index'),
    );

    public function isAllowedAction(array $acl, $controller, $action)
    {
        if (isset($acl[$controller])) {
            return in_array($action, $acl[$controller]);
        }

        return false;
    }

    public function isPublicAction($controller, $action)
    {
        return $this->isAllowedAction($this->public_actions, $controller, $action);
    }

    public function isUserAction($controller, $action)
    {
        return $this->isAllowedAction($this->user_actions, $controller, $action);
    }

    public function isAdminUser()
    {
        return isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] === '1';
    }

    public function isRegularUser()
    {
        return isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] === '0';
    }

    public function getUserId()
    {
        return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : 0;
    }

    public function isPageAccessAllowed($controller, $action)
    {
        return $this->isPublicAction($controller, $action) ||
               $this->isAdminUser() ||
               ($this->isRegularUser() && $this->isUserAction($controller, $action));
    }
}
