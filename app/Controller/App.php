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
     * Redirect to the project creation page or the board controller
     *
     * @access public
     */
    public function index()
    {
        if ($this->project->countByStatus(ProjectModel::ACTIVE)) {
            $this->response->redirect('?controller=board');
        }
        else {
            $this->redirectNoProject();
        }
    }
    
    public function dashboard()
    {
        // Get my projects
        $board_selector = $this->projectPermission->getAllowedProjects($this->acl->getUserId());

        // Get activity of tasks in my projects (including tasks assigned to others in my projects)
        $related_activity = $this->user->getUserProjectActivity($this->acl->getUserId());

        // Get tasks assigned to me
        $assigned_tasks = $this->task->getRecentTasksAssigned($this->acl->getUserId(), 25);

        // Get activity of tasks assigned to me
        $assigned_activity = $this->user->getUserAssignedActivity($this->acl->getUserId());


        $this->response->html($this->template->layout('app_dashboard', array(
            'menu' => 'dashboard',
            'board_selector' => $board_selector,
            'assigned_tasks' => $assigned_tasks,
            'assigned_activity' => $assigned_activity,
            'related_activity' => $related_activity
        )));
    }
}
