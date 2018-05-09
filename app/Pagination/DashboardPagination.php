<?php

namespace Kanboard\Pagination;

use Kanboard\Core\Base;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;

/**
 * Class DashboardPagination
 *
 * @package Kanboard\Pagination
 * @author  Frederic Guillot
 */
class DashboardPagination extends Base
{
    /**
     * Get user listing pagination
     *
     * @access public
     * @param  integer $userId
     * @return array
     */
    public function getOverview($userId)
    {
        $paginators = array();
        $projects = $this->projectUserRoleModel->getActiveProjectsByUser($userId);

        foreach ($projects as $projectId => $projectName) {

            $query = $this->taskFinderModel->getUserQuery($userId)->eq(ProjectModel::TABLE.'.id', $projectId);
            $this->hook->reference('pagination:dashboard:task:query', $query);

            $paginator = $this->paginator
                ->setUrl('DashboardController', 'show', array('user_id' => $userId, 'pagination' => 'tasks-'.$projectId), 'project-tasks-'.$projectId)
                ->setMax(15)
                ->setOrder(TaskModel::TABLE.'.priority')
                ->setDirection('DESC')
                ->setFormatter($this->taskListSubtaskAssigneeFormatter->withUserId($userId))
                ->setQuery($query)
                ->calculateOnlyIf($this->request->getStringParam('pagination') === 'tasks-'.$projectId);

            if ($paginator->getTotal() > 0) {
                $paginators[] = array(
                    'project_id'   => $projectId,
                    'project_name' => $projectName,
                    'paginator'    => $paginator,
                );
            }
        }

        return $paginators;
    }
}
