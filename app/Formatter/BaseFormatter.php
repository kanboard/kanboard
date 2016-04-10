<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Base;
use Kanboard\Core\Filter\FormatterInterface;
use PicoDb\Table;

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
     * Set query
     *
     * @access public
     * @param  Table $query
     * @return FormatterInterface
     */
    public function withQuery(Table $query)
    {
        $this->query = $query;
        return $this;
    }
}
