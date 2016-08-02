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
     * @param  integer $user_id
     * @param  string  $method
     * @param  integer $max
     * @return Paginator
     */
    public function getDashboardPaginator($user_id, $method, $max)
    {
        return $this->paginator
            ->setUrl('DashboardController', $method, array('pagination' => 'tasks', 'user_id' => $user_id))
            ->setMax($max)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setQuery($this->taskFinderModel->getUserQuery($user_id))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'tasks');
    }
}
