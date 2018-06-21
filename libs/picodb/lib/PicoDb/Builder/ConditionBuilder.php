<?php

namespace PicoDb\Builder;

use PicoDb\Database;
use PicoDb\Table;

/**
 * Handle SQL conditions
 *
 * @package PicoDb\Builder
 * @author  Frederic Guillot
 */
class ConditionBuilder
{
    /**
     * Database instance
     *
     * @access private
     * @var    Database
     */
    private $db;

    /**
     * Condition values
     *
     * @access private
     * @var    array
     */
    private $values = array();

    /**
     * SQL AND conditions
     *
     * @access private
     * @var    string[]
     */
    private $conditions = array();

    /**
     * SQL OR conditions
     *
     * @access private
     * @var    OrConditionBuilder[]
     */
    private $orConditions = array();

    /**
     * SQL condition offset
     *
     * @access private
     * @var int
     */
    private $orConditionOffset = 0;

    /**
     * Constructor
     *
     * @access public
     * @param  Database  $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Build the SQL condition
     *
     * @access public
     * @return string
     */
    public function build()
    {
        return empty($this->conditions) ? '' : ' WHERE '.implode(' AND ', $this->conditions);
    }

    /**
     * Get condition values
     *
     * @access public
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Returns true if there is some conditions
     *
     * @access public
     * @return boolean
     */
    public function hasCondition()
    {
        return ! empty($this->conditions);
    }

    /**
     * Add custom condition
     *
     * @access public
     * @param  string  $sql
     */
    public function addCondition($sql)
    {
        if ($this->orConditionOffset > 0) {
            $this->orConditions[$this->orConditionOffset]->withCondition($sql);
        }
        else {
            $this->conditions[] = $sql;
        }
    }

    /**
     * Start OR condition
     *
     * @access public
     */
    public function beginOr()
    {
        $this->orConditionOffset++;
        $this->orConditions[$this->orConditionOffset] = new OrConditionBuilder();
    }

    /**
     * Close OR condition
     *
     * @access public
     */
    public function closeOr()
    {
        $condition = $this->orConditions[$this->orConditionOffset]->build();
        $this->orConditionOffset--;

        if ($this->orConditionOffset > 0) {
            $this->orConditions[$this->orConditionOffset]->withCondition($condition);
        } else {
            $this->conditions[] = $condition;
        }
    }

    /**
     * Equal condition
     *
     * @access public
     * @param  string   $column
     * @param  mixed    $value
     */
    public function eq($column, $value)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' = ?');
        $this->values[] = $value;
    }

    /**
     * Not equal condition
     *
     * @access public
     * @param  string   $column
     * @param  mixed    $value
     */
    public function neq($column, $value)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' != ?');
        $this->values[] = $value;
    }

    /**
     * IN condition
     *
     * @access public
     * @param  string   $column
     * @param  array    $values
     */
    public function in($column, array $values)
    {
        if (! empty($values)) {
            $this->addCondition($this->db->escapeIdentifier($column).' IN ('.implode(', ', array_fill(0, count($values), '?')).')');
            $this->values = array_merge($this->values, $values);
        }
    }

    /**
     * IN condition with a subquery
     *
     * @access public
     * @param  string   $column
     * @param  Table    $subquery
     */
    public function inSubquery($column, Table $subquery)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' IN ('.$subquery->buildSelectQuery().')');
        $this->values = array_merge($this->values, $subquery->getConditionBuilder()->getValues());
    }

    /**
     * NOT IN condition
     *
     * @access public
     * @param  string   $column
     * @param  array    $values
     */
    public function notIn($column, array $values)
    {
        if (! empty($values)) {
            $this->addCondition($this->db->escapeIdentifier($column).' NOT IN ('.implode(', ', array_fill(0, count($values), '?')).')');
            $this->values = array_merge($this->values, $values);
        }
    }

    /**
     * NOT IN condition with a subquery
     *
     * @access public
     * @param  string   $column
     * @param  Table    $subquery
     */
    public function notInSubquery($column, Table $subquery)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' NOT IN ('.$subquery->buildSelectQuery().')');
        $this->values = array_merge($this->values, $subquery->getConditionBuilder()->getValues());
    }

    /**
     * LIKE condition
     *
     * @access public
     * @param  string   $column
     * @param  mixed    $value
     */
    public function like($column, $value)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' '.$this->db->getDriver()->getOperator('LIKE').' ?');
        $this->values[] = $value;
    }

    /**
     * ILIKE condition
     *
     * @access public
     * @param  string   $column
     * @param  mixed    $value
     */
    public function ilike($column, $value)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' '.$this->db->getDriver()->getOperator('ILIKE').' ?');
        $this->values[] = $value;
    }

    /**
     * Greater than condition
     *
     * @access public
     * @param  string   $column
     * @param  mixed    $value
     */
    public function gt($column, $value)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' > ?');
        $this->values[] = $value;
    }

    /**
     * Greater than condition with subquery
     *
     * @access public
     * @param  string   $column
     * @param  Table    $subquery
     */
    public function gtSubquery($column, Table $subquery)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' > ('.$subquery->buildSelectQuery().')');
        $this->values = array_merge($this->values, $subquery->getConditionBuilder()->getValues());
    }

    /**
     * Lower than condition
     *
     * @access public
     * @param  string   $column
     * @param  mixed    $value
     */
    public function lt($column, $value)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' < ?');
        $this->values[] = $value;
    }

    /**
     * Lower than condition with subquery
     *
     * @access public
     * @param  string   $column
     * @param  Table    $subquery
     */
    public function ltSubquery($column, Table $subquery)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' < ('.$subquery->buildSelectQuery().')');
        $this->values = array_merge($this->values, $subquery->getConditionBuilder()->getValues());
    }

    /**
     * Greater than or equals condition
     *
     * @access public
     * @param  string   $column
     * @param  mixed    $value
     */
    public function gte($column, $value)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' >= ?');
        $this->values[] = $value;
    }

    /**
     * Greater than or equal condition with subquery
     *
     * @access public
     * @param  string   $column
     * @param  Table    $subquery
     */
    public function gteSubquery($column, Table $subquery)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' >= ('.$subquery->buildSelectQuery().')');
        $this->values = array_merge($this->values, $subquery->getConditionBuilder()->getValues());
    }

    /**
     * Lower than or equals condition
     *
     * @access public
     * @param  string   $column
     * @param  mixed    $value
     */
    public function lte($column, $value)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' <= ?');
        $this->values[] = $value;
    }

    /**
     * Lower than or equal condition with subquery
     *
     * @access public
     * @param  string   $column
     * @param  Table    $subquery
     */
    public function lteSubquery($column, Table $subquery)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' <= ('.$subquery->buildSelectQuery().')');
        $this->values = array_merge($this->values, $subquery->getConditionBuilder()->getValues());
    }

    /**
     * IS NULL condition
     *
     * @access public
     * @param  string   $column
     */
    public function isNull($column)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' IS NULL');
    }

    /**
     * IS NOT NULL condition
     *
     * @access public
     * @param  string   $column
     */
    public function notNull($column)
    {
        $this->addCondition($this->db->escapeIdentifier($column).' IS NOT NULL');
    }
}
