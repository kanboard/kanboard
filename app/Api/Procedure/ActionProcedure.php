<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ActionAuthorization;
use Kanboard\Api\Authorization\ProjectAuthorization;

/**
 * Action API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class ActionProcedure extends BaseProcedure
{
    public function getAvailableActions()
    {
        return (object) $this->actionManager->getAvailableActions();
    }

    public function getAvailableActionEvents()
    {
        return (object) $this->eventManager->getAll();
    }

    public function getCompatibleActionEvents($action_name)
    {
        return (object) $this->actionManager->getCompatibleEvents($action_name);
    }

    public function removeAction($action_id)
    {
        ActionAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeAction', $action_id);
        return $this->actionModel->remove($action_id);
    }

    public function getActions($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getActions', $project_id);
        return $this->actionModel->getAllByProject($project_id);
    }

    public function createAction($project_id, $event_name, $action_name, array $params)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'createAction', $project_id);
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

        return $this->actionModel->create($values);
    }
}
