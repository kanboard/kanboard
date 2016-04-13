<?php

namespace Kanboard\Core\Filter;

use PicoDb\Table;

/**
 * Criteria Interface
 *
 * @package  filter
 * @author   Frederic Guillot
 */
interface CriteriaInterface
{
    /**
     * Set the Query
     *
     * @access public
     * @param Table $query
     * @return CriteriaInterface
     */
    public function withQuery(Table $query);

    /**
     * Set filter
     *
     * @access public
     * @param  FilterInterface $filter
     * @return CriteriaInterface
     */
    public function withFilter(FilterInterface $filter);

    /**
     * Apply condition
     *
     * @access public
     * @return CriteriaInterface
     */
    public function apply();
}
