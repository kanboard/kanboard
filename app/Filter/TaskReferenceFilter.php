<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TaskModel;

/**
 * Filter tasks by reference
 *
 * @package filter
 * @author  Frederic Guillot
 */
class TaskReferenceFilter extends BaseFilter implements FilterInterface
{
    /**
     * Escape character used to match the reference literally with LIKE
     *
     * @var string
     */
    const LIKE_ESCAPE_CHAR = '!';

    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('reference', 'ref');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        if ($this->value === 'none') {
            $this->query->beginOr();
            $this->query->eq(TaskModel::TABLE.'.reference', '');
            $this->query->isNull(TaskModel::TABLE.'.reference');
            $this->query->closeOr();
            return $this;
        }

        $this->query->ilike(TaskModel::TABLE.'.reference', $this->getPattern(), self::LIKE_ESCAPE_CHAR);
        return $this;
    }

    /**
     * Convert the search value to a LIKE pattern where only "*" is a wildcard
     *
     * The SQL wildcards "%" and "_" are matched literally, as well as "[" which starts a character range with SQL Server.
     *
     * @access protected
     * @return string
     */
    protected function getPattern()
    {
        $escape = self::LIKE_ESCAPE_CHAR;

        return strtr((string) $this->value, array(
            $escape => $escape.$escape,
            '%' => $escape.'%',
            '_' => $escape.'_',
            '[' => $escape.'[',
            '*' => '%',
        ));
    }
}
