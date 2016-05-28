<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\ProjectActivityModel;

/**
 * Filter activity events by creator
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectActivityCreatorFilter extends BaseFilter implements FilterInterface
{
    /**
     * Current user id
     *
     * @access private
     * @var int
     */
    private $currentUserId = 0;

    /**
     * Set current user id
     *
     * @access public
     * @param  integer $userId
     * @return TaskAssigneeFilter
     */
    public function setCurrentUserId($userId)
    {
        $this->currentUserId = $userId;
        return $this;
    }

    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('creator');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return string
     */
    public function apply()
    {
        if ($this->value === 'me') {
            $this->query->eq(ProjectActivityModel::TABLE . '.creator_id', $this->currentUserId);
        } else {
            $this->query->beginOr();
            $this->query->ilike('uc.username', '%'.$this->value.'%');
            $this->query->ilike('uc.name', '%'.$this->value.'%');
            $this->query->closeOr();
        }
    }
}
