<?php

namespace Kanboard\Controller;

/**
 * Project Overview Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class ProjectOverviewController extends BaseController
{
    /**
     * Show project overview
     */
    public function show()
    {
        $project = $this->getProject();
        $columns = $this->columnModel->getAllWithTaskCount($project['id']);
        $tasks = $this->taskModel->getAllTasksWithExpenses($project['id']);

        $this->response->html($this->helper->layout->app('project_overview/show', array(
            'project'     => $project,
            'columns'     => $columns,
            'title'       => $project['name'],
            'description' => $this->helper->projectHeader->getDescription($project),
            'users'       => $this->projectUserRoleModel->getAllUsersGroupedByRole($project['id']),
            'tasks'       => $tasks,
            'referenceCurrency' => $this->configModel->get('application_currency'),
            'roles'       => $this->projectRoleModel->getList($project['id']),
            'events'      => $this->helper->projectActivity->getProjectEvents($project['id'], 10),
            'images'      => $this->projectFileModel->getAllImages($project['id']),
            'files'       => $this->projectFileModel->getAllDocuments($project['id']),
        )));
    }
}
