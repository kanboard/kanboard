<?php

namespace Kanboard\Core\Filter;

use PicoDb\Table;

/**
 * OR criteria
 *
 * @package  filter
 * @author   Frederic Guillot
 */
class OrCriteria implements CriteriaInterface
{
    /**
     * @var Table
     */
    protected $query;

    /**
     * @var FilterInterface[]
     */
    protected $filters = array();

    /**
     * Set the Query
     *
     * @access public
     * @param  Table $query
     * @return CriteriaInterface
     */
    public function withQuery(Table $query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Set filter
     *
     * @access public
     * @param  FilterInterface $filter
     * @return CriteriaInterface
     */
    public function withFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * Apply condition
     *
     * @access public
     * @return CriteriaInterface
     */
    public function apply()
    {
        $this->query->beginOr();

        foreach ($this->filters as $filter) {
            $filter->withQuery($this->query)->apply();
        }

        $this->query->closeOr();
        return $this;
    }
}
