<?php

namespace Kanboard\Api;

use Kanboard\Core\Base;

/**
 * Swimlane API controller
 *
 * @package  Kanboard\Api
 * @author   Frederic Guillot
 */
class SwimlaneApi extends Base
{
    public function getActiveSwimlanes($project_id)
    {
        return $this->swimlaneModel->getSwimlanes($project_id);
    }

    public function getAllSwimlanes($project_id)
    {
        return $this->swimlaneModel->getAll($project_id);
    }

    public function getSwimlaneById($swimlane_id)
    {
        return $this->swimlaneModel->getById($swimlane_id);
    }

    public function getSwimlaneByName($project_id, $name)
    {
        return $this->swimlaneModel->getByName($project_id, $name);
    }

    public function getSwimlane($swimlane_id)
    {
        return $this->swimlaneModel->getById($swimlane_id);
    }

    public function getDefaultSwimlane($project_id)
    {
        return $this->swimlaneModel->getDefault($project_id);
    }

    public function addSwimlane($project_id, $name, $description = '')
    {
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
        return $this->swimlaneModel->remove($project_id, $swimlane_id);
    }

    public function disableSwimlane($project_id, $swimlane_id)
    {
        return $this->swimlaneModel->disable($project_id, $swimlane_id);
    }

    public function enableSwimlane($project_id, $swimlane_id)
    {
        return $this->swimlaneModel->enable($project_id, $swimlane_id);
    }

    public function changeSwimlanePosition($project_id, $swimlane_id, $position)
    {
        return $this->swimlaneModel->changePosition($project_id, $swimlane_id, $position);
    }
}
