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
        $projects = $this->projectPermission->getAllowedProjects($user_id);

        $this->response->html($this->template->layout('app_index', array(
            'board_selector' => $projects,
            'events' => $this->projectActivity->getProjects(array_keys($projects), 10),
            'tasks' => $this->taskFinder->getAllTasksByUser($user_id),
            'title' => t('Dashboard'),
        )));
    }
}
