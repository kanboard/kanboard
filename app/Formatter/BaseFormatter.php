<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Base;
use PicoDb\Table;
use Pimple\Container;

/**
 * Class BaseFormatter
 *
 * @package formatter
 * @author  Frederic Guillot
 */
abstract class BaseFormatter extends Base
{
    /**
     * Query object
     *
     * @access protected
     * @var Table
     */
    protected $query;

    /**
     * Get object instance
     *
     * @static
     * @access public
     * @param  Container $container
     * @return static
     */
    public static function getInstance(Container $container)
    {
        return new static($container);
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
}
