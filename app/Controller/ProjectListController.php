<?php

namespace Kanboard\Controller;

/**
 * Class ProjectListController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class ProjectListController extends BaseController
{
    /**
     * List of projects
     *
     * @access public
     */
    public function show()
    {
        if ($this->userSession->isAdmin()) {
            $project_ids = $this->project->getAllIds();
        } else {
            $project_ids = $this->projectPermission->getActiveProjectIds($this->userSession->getId());
        }

        $nb_projects = count($project_ids);

        $paginator = $this->paginator
            ->setUrl('ProjectListController', 'show')
            ->setMax(20)
            ->setOrder('name')
            ->setQuery($this->project->getQueryColumnStats($project_ids))
            ->calculate();

        $this->response->html($this->helper->layout->app('project_list/show', array(
            'paginator' => $paginator,
            'nb_projects' => $nb_projects,
            'title' => t('Projects').' ('.$nb_projects.')'
        )));
    }
}
