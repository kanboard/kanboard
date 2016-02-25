<?php

namespace Kanboard\Core\Action;

use RuntimeException;
use Kanboard\Core\Base;
use Kanboard\Action\Base as ActionBase;

/**
 * Action Manager
 *
 * @package  action
 * @author   Frederic Guillot
 */
class ActionManager extends Base
{
    /**
     * List of automatic actions
     *
     * @access private
     * @var array
     */
    private $actions = array();

    /**
     * Register a new automatic action
     *
     * @access public
     * @param  ActionBase $action
     * @return ActionManager
     */
    public function register(ActionBase $action)
    {
        $this->actions[$action->getName()] = $action;
        return $this;
    }

    /**
     * Get automatic action instance
     *
     * @access public
     * @param  string  $name  Absolute class name with namespace
     * @return ActionBase
     */
    public function getAction($name)
    {
        if (isset($this->actions[$name])) {
            return $this->actions[$name];
        }

        throw new RuntimeException('Automatic Action Not Found: '.$name);
    }

    /**
     * Get available automatic actions
     *
     * @access public
     * @return array
     */
    public function getAvailableActions()
    {
        $actions = array();

        foreach ($this->actions as $action) {
            if (count($action->getEvents()) > 0) {
                $actions[$action->getName()] = $action->getDescription();
            }
        }

        asort($actions);

        return $actions;
    }

    /**
     * Get all available action parameters
     *
     * @access public
     * @param  array  $actions
     * @return array
     */
    public function getAvailableParameters(array $actions)
    {
        $params = array();

        foreach ($actions as $action) {
            $currentAction = $this->getAction($action['action_name']);
            $params[$currentAction->getName()] = $currentAction->getActionRequiredParameters();
        }

        return $params;
    }

    /**
     * Get list of compatible events for a given action
     *
     * @access public
     * @param  string $name
     * @return array
     */
    public function getCompatibleEvents($name)
    {
        $events = array();
        $actionEvents = $this->getAction($name)->getEvents();

        foreach ($this->eventManager->getAll() as $event => $description) {
            if (in_array($event, $actionEvents)) {
                $events[$event] = $description;
            }
        }

        return $events;
    }

    /**
     * Bind automatic actions to events
     *
     * @access public
     * @return ActionManager
     */
    public function attachEvents()
    {
        if ($this->userSession->isLogged()) {
            $actions = $this->action->getAllByUser($this->userSession->getId());
        } else {
            $actions = $this->action->getAll();
        }

        foreach ($actions as $action) {
            $listener = clone $this->getAction($action['action_name']);
            $listener->setProjectId($action['project_id']);

            foreach ($action['params'] as $param_name => $param_value) {
                $listener->setParam($param_name, $param_value);
            }

            $this->dispatcher->addListener($action['event_name'], array($listener, 'execute'));
        }

        return $this;
    }
}
