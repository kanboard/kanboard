<?php

namespace Controller;

use Model\Project as ProjectModel;

/**
 * Application controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class App extends Base
{
    /**
     * Dashboard for the current user
     *
     * @access public
     */
    public function index()
    {
        $user_id = $this->acl->getUserId();
        $projects = $this->projectPermission->getMemberProjects($user_id);
        $project_ids = array_keys($projects);

        $this->response->html($this->template->layout('app/index', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($user_id),
            'events' => $this->projectActivity->getProjects($project_ids, 10),
            'tasks' => $this->taskFinder->getAllTasksByUser($user_id),
            'projects' => $this->project->getSummary($project_ids),
            'title' => t('Dashboard'),
        )));
    }
}
