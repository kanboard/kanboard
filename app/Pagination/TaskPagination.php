<?php

namespace Kanboard\Pagination;

use Kanboard\Core\Base;
use Kanboard\Core\Paginator;
use Kanboard\Model\TaskModel;

/**
 * Class TaskPagination
 *
 * @package Kanboard\Pagination
 * @author  Frederic Guillot
 */
class TaskPagination extends Base
{
    /**
     * Get dashboard pagination
     *
     * @access public
     * @param  integer $userId
     * @param  string  $method
     * @param  integer $max
     * @return Paginator
     */
    public function getDashboardPaginator($userId, $method, $max)
    {
        $query = $this->taskFinderModel->getUserQuery($userId);
        $this->hook->reference('pagination:dashboard:task:query', $query);

        return $this->paginator
            ->setUrl('DashboardController', $method, array('pagination' => 'tasks', 'user_id' => $userId))
            ->setMax($max)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setQuery($query)
            ->setFormatter($this->taskListFormatter)
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'tasks');
    }
}
