<?php

namespace Kanboard\Core\Filter;

use PicoDb\Table;

/**
 * Lexer Builder
 *
 * @package filter
 * @author  Frederic Guillot
 */
class LexerBuilder
{
    /**
     * Lexer object
     *
     * @access protected
     * @var Lexer
     */
    protected $lexer;

    /**
     * Query object
     *
     * @access protected
     * @var Table
     */
    protected $query;

    /**
     * List of filters
     *
     * @access protected
     * @var FilterInterface[]
     */
    protected $filters;

    /**
     * QueryBuilder object
     *
     * @access protected
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->lexer = new Lexer();
        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * Add a filter
     *
     * @access public
     * @param  FilterInterface $filter
     * @param  bool            $default
     * @return LexerBuilder
     */
    public function withFilter(FilterInterface $filter, $default = false)
    {
        $attributes = $filter->getAttributes();

        foreach ($attributes as $attribute) {
            $this->filters[$attribute] = $filter;
            $this->lexer->addToken(sprintf("/^(%s:)/i", $attribute), $attribute);

            if ($default) {
                $this->lexer->setDefaultToken($attribute);
            }
        }

        return $this;
    }

    /**
     * Set the query
     *
     * @access public
     * @param  Table $query
     * @return LexerBuilder
     */
    public function withQuery(Table $query)
    {
        $this->query = $query;
        $this->queryBuilder->withQuery($this->query);
        return $this;
    }

    /**
     * Parse the input and build the query
     *
     * @access public
     * @param  string $input
     * @return QueryBuilder
     */
    public function build($input)
    {
        $tokens = $this->lexer->tokenize($input);

        foreach ($tokens as $token => $values) {
            if (isset($this->filters[$token])) {
                $this->applyFilters($this->filters[$token], $values);
            }
        }

        return $this->queryBuilder;
    }

    /**
     * Apply filters to the query
     *
     * @access protected
     * @param  FilterInterface $filter
     * @param  array           $values
     */
    protected function applyFilters(FilterInterface $filter, array $values)
    {
        $len = count($values);

        if ($len > 1) {
            $criteria = new OrCriteria();
            $criteria->withQuery($this->query);

            foreach ($values as $value) {
                $currentFilter = clone($filter);
                $criteria->withFilter($currentFilter->withValue($value));
            }

            $this->queryBuilder->withCriteria($criteria);
        } elseif ($len === 1) {
            $this->queryBuilder->withFilter($filter->withValue($values[0]));
        }
    }

    /**
     * Clone object with deep copy
     */
    public function __clone()
    {
        $this->lexer = clone $this->lexer;
        $this->query = clone $this->query;
        $this->queryBuilder = clone $this->queryBuilder;
    }
}
