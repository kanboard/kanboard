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
            $projectIds = $this->projectModel->getAllIds();
        } else {
            $projectIds = $this->projectPermissionModel->getProjectIds($this->userSession->getId());
        }

        $query = $this->projectModel->getQueryByProjectIds($projectIds);
        $search = $this->request->getStringParam('search');

        if ($search !== '') {
            $query->ilike('projects.name', '%' . $search . '%');
        }

        $paginator = $this->paginator
            ->setUrl('ProjectListController', 'show')
            ->setMax(20)
            ->setOrder('name')
            ->setQuery($query)
            ->calculate();

        $this->response->html($this->helper->layout->app('project_list/listing', array(
            'paginator'   => $paginator,
            'title'       => t('Projects') . ' (' . $paginator->getTotal() . ')',
            'values'      => array('search' => $search),
        )));
    }
}
