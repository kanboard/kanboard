<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ProjectAuthorization;
use Kanboard\Model\SwimlaneModel;

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
        return $this->swimlaneModel->getAllByStatus($project_id, SwimlaneModel::ACTIVE);
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

    public function addSwimlane($project_id, $name, $description = '')
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'addSwimlane', $project_id);
        return $this->swimlaneModel->create($project_id, $name, $description);
    }

    public function updateSwimlane($project_id, $swimlane_id, $name, $description = null)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'updateSwimlane', $project_id);

        $values = array(
            'project_id' => $project_id,
            'id'         => $swimlane_id,
            'name'       => $name,
        );

        if (! is_null($description)) {
            $values['description'] = $description;
        }

        list($valid, $errors) = $this->swimlaneValidator->validateModification($values);

        if (! $valid) {
            $this->logger->debug(__METHOD__.': Validation error: '.var_export($errors, true));
            return false;
        }

        return $this->swimlaneModel->update($swimlane_id, $values);
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
