<?php

namespace Event;

/**
 * Project modification date listener
 *
 * Update the "last_modified" field for a project
 *
 * @package event
 * @author  Frederic Guillot
 */
class ProjectModificationDateListener extends Base
{
    /**
     * Execute the action
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function execute(array $data)
    {
        if (isset($data['project_id'])) {
            return $this->project->updateModificationDate($data['project_id']);
        }

        return false;
    }
}
