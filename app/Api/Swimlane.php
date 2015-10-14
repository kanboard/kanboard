<?php

namespace Kanboard\Api;

/**
 * Swimlane API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class Swimlane extends \Kanboard\Core\Base
{
    public function getActiveSwimlanes($project_id)
    {
        return $this->swimlane->getSwimlanes($project_id);
    }

    public function getAllSwimlanes($project_id)
    {
        return $this->swimlane->getAll($project_id);
    }

    public function getSwimlaneById($swimlane_id)
    {
        return $this->swimlane->getById($swimlane_id);
    }

    public function getSwimlaneByName($project_id, $name)
    {
        return $this->swimlane->getByName($project_id, $name);
    }

    public function getSwimlane($swimlane_id)
    {
        return $this->swimlane->getById($swimlane_id);
    }

    public function getDefaultSwimlane($project_id)
    {
        return $this->swimlane->getDefault($project_id);
    }

    public function addSwimlane($project_id, $name, $description = '')
    {
        return $this->swimlane->create(array('project_id' => $project_id, 'name' => $name, 'description' => $description));
    }

    public function updateSwimlane($swimlane_id, $name, $description = null)
    {
        $values = array('id' => $swimlane_id, 'name' => $name);
        if (!is_null($description)) {
            $values['description'] = $description;
        }
        return $this->swimlane->update($values);
    }

    public function removeSwimlane($project_id, $swimlane_id)
    {
        return $this->swimlane->remove($project_id, $swimlane_id);
    }

    public function disableSwimlane($project_id, $swimlane_id)
    {
        return $this->swimlane->disable($project_id, $swimlane_id);
    }

    public function enableSwimlane($project_id, $swimlane_id)
    {
        return $this->swimlane->enable($project_id, $swimlane_id);
    }

    public function moveSwimlaneUp($project_id, $swimlane_id)
    {
        return $this->swimlane->moveUp($project_id, $swimlane_id);
    }

    public function moveSwimlaneDown($project_id, $swimlane_id)
    {
        return $this->swimlane->moveDown($project_id, $swimlane_id);
    }
}
