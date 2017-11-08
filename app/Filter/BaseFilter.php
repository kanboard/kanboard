<?php

namespace Kanboard\Filter;

use PicoDb\Table;

/**
 * Base filter class
 *
 * @package filter
 * @author  Frederic Guillot
 */
abstract class BaseFilter
{
    /**
     * @var Table
     */
    protected $query;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * BaseFilter constructor
     *
     * @access public
     * @param  mixed $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * Get object instance
     *
     * @static
     * @access public
     * @param  mixed $value
     * @return static
     */
    public static function getInstance($value = null)
    {
        return new static($value);
    }

    /**
     * Set query
     *
     * @access public
     * @param  Table $query
     * @return $this
     */
    public function withQuery(Table $query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Set the value
     *
     * @access public
     * @param  string $value
     * @return $this
     */
    public function withValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
