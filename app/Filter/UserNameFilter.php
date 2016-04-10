<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;

class UserNameFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('name');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->beginOr()
            ->ilike('username', '%'.$this->value.'%')
            ->ilike('name', '%'.$this->value.'%')
            ->closeOr();

        return $this;
    }
}
