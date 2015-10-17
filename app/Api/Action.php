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
        return $this->action->getAvailableActions();
    }

    public function getAvailableActionEvents()
    {
        return $this->action->getAvailableEvents();
    }

    public function getCompatibleActionEvents($action_name)
    {
        return $this->action->getCompatibleEvents($action_name);
    }

    public function removeAction($action_id)
    {
        return $this->action->remove($action_id);
    }

    public function getActions($project_id)
    {
        $actions = $this->action->getAllByProject($project_id);

        foreach ($actions as $index => $action) {
            $params = array();

            foreach ($action['params'] as $param) {
                $params[$param['name']] = $param['value'];
            }

            $actions[$index]['params'] = $params;
        }

        return $actions;
    }

    public function createAction($project_id, $event_name, $action_name, $params)
    {
        $values = array(
            'project_id' => $project_id,
            'event_name' => $event_name,
            'action_name' => $action_name,
            'params' => $params,
        );

        list($valid, ) = $this->action->validateCreation($values);

        if (! $valid) {
            return false;
        }

        // Check if the action exists
        $actions = $this->action->getAvailableActions();

        if (! isset($actions[$action_name])) {
            return false;
        }

        // Check the event
        $action = $this->action->load($action_name, $project_id, $event_name);

        if (! in_array($event_name, $action->getCompatibleEvents())) {
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
