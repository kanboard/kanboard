<?php

namespace Kanboard\Core\Filter;

use PicoDb\Table;

/**
 * Formatter interface
 *
 * @package  filter
 * @author   Frederic Guillot
 */
interface FormatterInterface
{
    /**
     * Set query
     *
     * @access public
     * @param  Table $query
     * @return $this
     */
    public function withQuery(Table $query);

    /**
     * Apply formatter
     *
     * @access public
     * @return mixed
     */
    public function format();
}
