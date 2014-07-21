<?php

namespace Event;

use Core\Listener;
use Model\Project;

/**
 * Project modification date listener
 *
 * Update the last modified field for a project
 *
 * @package event
 * @author  Frederic Guillot
 */
class ProjectModificationDate implements Listener
{
    /**
     * Project model
     *
     * @accesss private
     * @var \Model\Project
     */
    private $project;

    /**
     * Constructor
     *
     * @access public
     * @param  \Model\Project   $project   Project model instance
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

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
            $this->project->updateModificationDate($data['project_id']);
            return true;
        }

        return false;
    }
}
