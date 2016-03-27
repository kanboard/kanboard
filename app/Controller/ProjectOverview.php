<?php

namespace Kanboard\Controller;

/**
 * Project Overview Controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class ProjectOverview extends Base
{
    /**
     * Show project overview
     */
    public function show()
    {
        $project = $this->getProject();
        $this->project->getColumnStats($project);

        $this->response->html($this->helper->layout->app('project_overview/show', array(
            'project' => $project,
            'title' => $project['name'],
            'description' => $this->helper->projectHeader->getDescription($project),
            'users' => $this->projectUserRole->getAllUsersGroupedByRole($project['id']),
            'roles' => $this->role->getProjectRoles(),
            'events' => $this->projectActivity->getProject($project['id'], 10),
            'images' => $this->projectFile->getAllImages($project['id']),
            'files' => $this->projectFile->getAllDocuments($project['id']),
        )));
    }
}
