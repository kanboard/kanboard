<?php

namespace Kanboard\Api;

/**
 * Action API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class Action extends \Kanboard\Core\Base
{
    public function getAvailableActions()
    {
        return $this->actionManager->getAvailableActions();
    }

    public function getAvailableActionEvents()
    {
        return $this->eventManager->getAll();
    }

    public function getCompatibleActionEvents($action_name)
    {
        return $this->actionManager->getCompatibleEvents($action_name);
    }

    public function removeAction($action_id)
    {
        return $this->action->remove($action_id);
    }

    public function getActions($project_id)
    {
        return $this->action->getAllByProject($project_id);
    }

    public function createAction($project_id, $event_name, $action_name, array $params)
    {
        $values = array(
            'project_id' => $project_id,
            'event_name' => $event_name,
            'action_name' => $action_name,
            'params' => $params,
        );

        list($valid, ) = $this->actionValidator->validateCreation($values);

        if (! $valid) {
            return false;
        }

        // Check if the action exists
        $actions = $this->actionManager->getAvailableActions();

        if (! isset($actions[$action_name])) {
            return false;
        }

        // Check the event
        $action = $this->actionManager->getAction($action_name);

        if (! in_array($event_name, $action->getEvents())) {
            return false;
        }

        $required_params = $action->getActionRequiredParameters();

        // Check missing parameters
        foreach ($required_params as $param => $value) {
            if (! isset($params[$param])) {
                return false;
            }
        }

        // Check extra parameters
        foreach ($params as $param => $value) {
            if (! isset($required_params[$param])) {
                return false;
            }
        }

        return $this->action->create($values);
    }
}
