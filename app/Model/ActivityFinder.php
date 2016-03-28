<?php

namespace Kanboard\Model;

/**
 * Activity Finder model
 *
 * @package  model
 * @author   Asim Husanovic
 */
class ActivityFinder extends Base
{
    /**
     * Extended query
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function getExtendedQuery()
    {
        return $this->db
            ->table(ProjectActivity::TABLE)
            ->join(Project::TABLE, 'id', 'project_id', ProjectActivity::TABLE)
            ->join(User::TABLE, 'id', 'creator_id')
            ->join(Task::TABLE, 'id', 'task_id');
    }
}
