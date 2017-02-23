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

        $paginator = $this->paginator
            ->setUrl('ProjectListController', 'show')
            ->setMax(20)
            ->setOrder('name')
            ->setQuery($this->projectModel->getQueryByProjectIds($projectIds))
            ->calculate();

        $this->response->html($this->helper->layout->app('project_list/listing', array(
            'paginator'   => $paginator,
            'title'       => t('Projects') . ' (' . $paginator->getTotal() . ')',
        )));
    }
}
