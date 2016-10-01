<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ProjectAuthorization;

/**
 * Swimlane API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class SwimlaneProcedure extends BaseProcedure
{
    public function getActiveSwimlanes($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getActiveSwimlanes', $project_id);
        return $this->swimlaneModel->getSwimlanes($project_id);
    }

    public function getAllSwimlanes($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getAllSwimlanes', $project_id);
        return $this->swimlaneModel->getAll($project_id);
    }

    public function getSwimlaneById($swimlane_id)
    {
        $swimlane = $this->swimlaneModel->getById($swimlane_id);
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getSwimlaneById', $swimlane['project_id']);
        return $swimlane;
    }

    public function getSwimlaneByName($project_id, $name)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getSwimlaneByName', $project_id);
        return $this->swimlaneModel->getByName($project_id, $name);
    }

    public function getSwimlane($swimlane_id)
    {
        return $this->swimlaneModel->getById($swimlane_id);
    }

    public function getDefaultSwimlane($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getDefaultSwimlane', $project_id);
        return $this->swimlaneModel->getDefault($project_id);
    }

    public function addSwimlane($project_id, $name, $description = '')
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'addSwimlane', $project_id);
        return $this->swimlaneModel->create(array('project_id' => $project_id, 'name' => $name, 'description' => $description));
    }

    public function updateSwimlane($swimlane_id, $name, $description = null)
    {
        $values = array('id' => $swimlane_id, 'name' => $name);

        if (!is_null($description)) {
            $values['description'] = $description;
        }

        return $this->swimlaneModel->update($values);
    }

    public function removeSwimlane($project_id, $swimlane_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeSwimlane', $project_id);
        return $this->swimlaneModel->remove($project_id, $swimlane_id);
    }

    public function disableSwimlane($project_id, $swimlane_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'disableSwimlane', $project_id);
        return $this->swimlaneModel->disable($project_id, $swimlane_id);
    }

    public function enableSwimlane($project_id, $swimlane_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'enableSwimlane', $project_id);
        return $this->swimlaneModel->enable($project_id, $swimlane_id);
    }

    public function changeSwimlanePosition($project_id, $swimlane_id, $position)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'changeSwimlanePosition', $project_id);
        return $this->swimlaneModel->changePosition($project_id, $swimlane_id, $position);
    }
}
