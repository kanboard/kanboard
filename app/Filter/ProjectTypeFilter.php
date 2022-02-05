<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\ProjectModel;

/**
 * Filter project by type
 *
 * @package filter
 * @author  Frederic Guillot
 */
class ProjectTypeFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('type');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        if (is_int($this->value) || ctype_digit((string) $this->value)) {
            $this->query->eq(ProjectModel::TABLE.'.is_private', $this->value);
        } elseif ($this->value === 'private') {
            $this->query->eq(ProjectModel::TABLE.'.is_private', ProjectModel::TYPE_PRIVATE);
        } else {
            $this->query->eq(ProjectModel::TABLE.'.is_private', ProjectModel::TYPE_TEAM);
        }

        return $this;
    }
}
