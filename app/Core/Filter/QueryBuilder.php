<?php

namespace Kanboard\Core\Filter;

use PicoDb\Table;

/**
 * Class QueryBuilder
 *
 * @package filter
 * @author  Frederic Guillot
 */
class QueryBuilder
{
    /**
     * Query object
     *
     * @access protected
     * @var Table
     */
    protected $query;

    /**
     * Create a new class instance
     *
     * @static
     * @access public
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Set the query
     *
     * @access public
     * @param  Table $query
     * @return QueryBuilder
     */
    public function withQuery(Table $query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Set a filter
     *
     * @access public
     * @param  FilterInterface $filter
     * @return QueryBuilder
     */
    public function withFilter(FilterInterface $filter)
    {
        $filter->withQuery($this->query)->apply();
        return $this;
    }

    /**
     * Set a criteria
     *
     * @access public
     * @param  CriteriaInterface $criteria
     * @return QueryBuilder
     */
    public function withCriteria(CriteriaInterface $criteria)
    {
        $criteria->withQuery($this->query)->apply();
        return $this;
    }

    /**
     * Set a formatter
     *
     * @access public
     * @param  FormatterInterface $formatter
     * @return string|array
     */
    public function format(FormatterInterface $formatter)
    {
        return $formatter->withQuery($this->query)->format();
    }

    /**
     * Get the query result as array
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        return $this->query->findAll();
    }

    /**
     * Get Query object
     *
     * @access public
     * @return Table
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Clone object with deep copy
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }
}
