<?php

namespace Kanboard\Filter;

/**
 * Base comparison filter class
 *
 * @package filter
 */
abstract class BaseComparisonFilter extends BaseFilter
{
    /**
     * Parse operator in the input string
     *
     * @access protected
     * @return string
     */
    protected function parseOperator()
    {
        $operators = array(
            '<=' => 'lte',
            '>=' => 'gte',
            '<' => 'lt',
            '>' => 'gt',
        );

        foreach ($operators as $operator => $method) {
            if (strpos($this->value, $operator) === 0) {
                $this->value = substr($this->value, strlen($operator));
                return $method;
            }
        }

        return 'eq';
    }

    /**
     * Apply a comparison filter
     *
     * @access protected
     * @param  string $field
     */
    protected function applyComparisonFilter($field)
    {
        $method = $this->parseOperator();
        $this->query->$method($field, $this->value);
    }
}
