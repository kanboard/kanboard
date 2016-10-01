<?php

namespace Kanboard\Core\Filter;

use PicoDb\Table;

/**
 * Filter Interface
 *
 * @package  filter
 * @author   Frederic Guillot
 */
interface FilterInterface
{
    /**
     * BaseFilter constructor
     *
     * @access public
     * @param  mixed $value
     */
    public function __construct($value = null);

    /**
     * Set the value
     *
     * @access public
     * @param  string $value
     * @return FilterInterface
     */
    public function withValue($value);

    /**
     * Set query
     *
     * @access public
     * @param  Table $query
     * @return FilterInterface
     */
    public function withQuery(Table $query);

    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes();

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply();
}
