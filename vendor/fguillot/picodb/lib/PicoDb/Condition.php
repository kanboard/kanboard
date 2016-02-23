<?php

namespace PicoDb;

/**
 * Handle SQL conditions
 *
 * @author   Frederic Guillot
 */
class Condition
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
     * SQL conditions
     *
     * @access private
     * @var    array
     */
    private $conditions = array();

    /**
     * SQL OR conditions
     *
     * @access private
     * @var    array
     */
    private $or = array();

    /**
     * OR condition started
     *
     * @access private
     * @var    boolean
     */
    private $beginOr = false;

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
        if ($this->beginOr) {
            $this->or[] = $sql;
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
        $this->beginOr = true;
        $this->or = array();
    }
    /**
     * Close OR condition
     *
     * @access public
     */
    public function closeOr()
    {
        $this->beginOr = false;

        if (! empty($this->or)) {
            $this->conditions[] = '('.implode(' OR ', $this->or).')';
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
        $this->values = array_merge($this->values, $subquery->condition->getValues());
    }

    /**
     * NOT IN condition
     *
     * @access public
     * @param  string   $column
     * @param  array    $values
     */
    public function notin($column, array $values)
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
        $this->values = array_merge($this->values, $subquery->condition->getValues());
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
