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
        $params = $this->getProjectFilters('ProjectOverview', 'show');
        $params['users'] = $this->projectUserRole->getAllUsersGroupedByRole($params['project']['id']);
        $params['roles'] = $this->role->getProjectRoles();
        $params['events'] = $this->projectActivity->getProject($params['project']['id'], 10);
        $params['images'] = $this->projectFile->getAllImages($params['project']['id']);
        $params['files'] = $this->projectFile->getAllDocuments($params['project']['id']);

        $this->project->getColumnStats($params['project']);

        $this->response->html($this->helper->layout->app('project_overview/show', $params));
    }
}
