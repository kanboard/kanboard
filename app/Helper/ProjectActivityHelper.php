<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;
use Kanboard\Filter\ProjectActivityProjectIdFilter;
use Kanboard\Filter\ProjectActivityProjectIdsFilter;
use Kanboard\Filter\ProjectActivityTaskIdFilter;
use Kanboard\Model\ProjectActivityModel;

/**
 * Project Activity Helper
 *
 * @package helper
 * @author  Frederic Guillot
 */
class ProjectActivityHelper extends Base
{
    /**
     * Search events
     *
     * @access public
     * @param  string $search
     * @return array
     */
    public function searchEvents($search)
    {
        $projects = $this->projectUserRoleModel->getActiveProjectsByUser($this->userSession->getId());
        $events = array();

        if ($search !== '') {
            $queryBuilder = $this->projectActivityLexer->build($search);
            $queryBuilder
                ->withFilter(new ProjectActivityProjectIdsFilter(array_keys($projects)))
                ->getQuery()
                ->desc(ProjectActivityModel::TABLE.'.id')
                ->limit(500)
            ;

            $events = $queryBuilder->format($this->projectActivityEventFormatter);
        }

        return $events;
    }

    /**
     * Get project activity events
     *
     * @access public
     * @param  integer  $project_id
     * @param  int      $limit
     * @return array
     */
    public function getProjectEvents($project_id, $limit = 50)
    {
        $queryBuilder = $this->projectActivityQuery
            ->withFilter(new ProjectActivityProjectIdFilter($project_id));

        $queryBuilder->getQuery()
            ->desc(ProjectActivityModel::TABLE.'.id')
            ->limit($limit)
        ;

        return $queryBuilder->format($this->projectActivityEventFormatter);
    }

    /**
     * Get projects activity events
     *
     * @access public
     * @param  int[]    $project_ids
     * @param  int      $limit
     * @return array
     */
    public function getProjectsEvents(array $project_ids, $limit = 50)
    {
        $queryBuilder = $this->projectActivityQuery
            ->withFilter(new ProjectActivityProjectIdsFilter($project_ids));

        $queryBuilder->getQuery()
            ->desc(ProjectActivityModel::TABLE.'.id')
            ->limit($limit)
        ;

        return $queryBuilder->format($this->projectActivityEventFormatter);
    }

    /**
     * Get task activity events
     *
     * @access public
     * @param  integer $task_id
     * @return array
     */
    public function getTaskEvents($task_id)
    {
        $queryBuilder = $this->projectActivityQuery
            ->withFilter(new ProjectActivityTaskIdFilter($task_id));

        $queryBuilder->getQuery()->desc(ProjectActivityModel::TABLE.'.id');

        return $queryBuilder->format($this->projectActivityEventFormatter);
    }
}
