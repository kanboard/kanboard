<?php

namespace Kanboard\Pagination;

use Kanboard\Core\Base;
use Kanboard\Core\Paginator;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\TaskModel;

/**
 * Class SubtaskPagination
 *
 * @package Kanboard\Pagination
 * @author  Frederic Guillot
 */
class SubtaskPagination extends Base
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
        $query = $this->subtaskModel->getUserQuery($user_id, array(SubtaskModel::STATUS_TODO, SubtaskModel::STATUS_INPROGRESS));
        $this->hook->reference('pagination:dashboard:subtask:query', $query);

        return $this->paginator
            ->setUrl('DashboardController', $method, array('pagination' => 'subtasks', 'user_id' => $user_id))
            ->setMax($max)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setQuery($query)
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'subtasks');
    }
}
