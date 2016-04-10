<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;
use Kanboard\Filter\ProjectActivityProjectIdFilter;
use Kanboard\Filter\ProjectActivityProjectIdsFilter;
use Kanboard\Filter\ProjectActivityTaskIdFilter;
use Kanboard\Formatter\ProjectActivityEventFormatter;
use Kanboard\Model\ProjectActivity;

/**
 * Project Activity Helper
 *
 * @package helper
 * @author  Frederic Guillot
 */
class ProjectActivityHelper extends Base
{
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
            ->desc(ProjectActivity::TABLE.'.id')
            ->limit($limit)
        ;

        return $queryBuilder->format(new ProjectActivityEventFormatter($this->container));
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
            ->desc(ProjectActivity::TABLE.'.id')
            ->limit($limit)
        ;

        return $queryBuilder->format(new ProjectActivityEventFormatter($this->container));
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

        $queryBuilder->getQuery()->desc(ProjectActivity::TABLE.'.id');

        return $queryBuilder->format(new ProjectActivityEventFormatter($this->container));
    }
}
