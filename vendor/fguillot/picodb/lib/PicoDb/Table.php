<?php

namespace PicoDb;

use PDO;
use Closure;
use PicoDb\Builder\ConditionBuilder;
use PicoDb\Builder\InsertBuilder;
use PicoDb\Builder\UpdateBuilder;

/**
 * Table
 *
 * @package PicoDb
 * @author  Frederic Guillot
 *
 * @method   $this   addCondition($sql)
 * @method   $this   beginOr()
 * @method   $this   closeOr()
 * @method   $this   eq($column, $value)
 * @method   $this   neq($column, $value)
 * @method   $this   in($column, array $values)
 * @method   $this   inSubquery($column, Table $subquery)
 * @method   $this   notIn($column, array $values)
 * @method   $this   notInSubquery($column, Table $subquery)
 * @method   $this   like($column, $value)
 * @method   $this   ilike($column, $value)
 * @method   $this   gt($column, $value)
 * @method   $this   gtSubquery($column, Table $subquery)
 * @method   $this   lt($column, $value)
 * @method   $this   ltSubquery($column, Table $subquery)
 * @method   $this   gte($column, $value)
 * @method   $this   gteSubquery($column, Table $subquery)
 * @method   $this   lte($column, $value)
 * @method   $this   lteSubquery($column, Table $subquery)
 * @method   $this   isNull($column)
 * @method   $this   notNull($column)
 */
class Table
{
    /**
     * Sorting direction
     *
     * @access public
     * @var string
     */
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    /**
     * Condition instance
     *
     * @access protected
     * @var    ConditionBuilder
     */
    protected $conditionBuilder;

    /**
     * Database instance
     *
     * @access protected
     * @var    Database
     */
    protected $db;

    /**
     * Table name
     *
     * @access protected
     * @var    string
     */
    protected $name = '';

    /**
     * Columns list for SELECT query
     *
     * @access private
     * @var    array
     */
    private $columns = array();

    /**
     * Columns to sum during update
     *
     * @access private
     * @var    array
     */
    private $sumColumns = array();

    /**
     * SQL limit
     *
     * @access private
     * @var    string
     */
    private $sqlLimit = '';

    /**
     * SQL offset
     *
     * @access private
     * @var    string
     */
    private $sqlOffset = '';

    /**
     * SQL order
     *
     * @access private
     * @var    string
     */
    private $sqlOrder = '';

    /**
     * SQL custom SELECT value
     *
     * @access private
     * @var    string
     */
    private $sqlSelect = '';

    /**
     * SQL joins
     *
     * @access private
     * @var    array
     */
    private $joins = array();

    /**
     * Use DISTINCT or not?
     *
     * @access private
     * @var    boolean
     */
    private $distinct = false;

    /**
     * Group by those columns
     *
     * @access private
     * @var    array
     */
    private $groupBy = array();

    /**
     * Callback for result filtering
     *
     * @access private
     * @var    Closure
     */
    private $callback = null;

    /**
     * Constructor
     *
     * @access public
     * @param  Database   $db
     * @param  string     $name
     */
    public function __construct(Database $db, $name)
    {
        $this->db = $db;
        $this->name = $name;
        $this->conditionBuilder = new ConditionBuilder($db);
    }

    /**
     * Return the table name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return ConditionBuilder object
     *
     * @access public
     * @return ConditionBuilder
     */
    public function getConditionBuilder()
    {
        return $this->conditionBuilder;
    }

    /**
     * Insert or update
     *
     * @access public
     * @param  array    $data
     * @return boolean
     */
    public function save(array $data)
    {
        return $this->conditionBuilder->hasCondition() ? $this->update($data) : $this->insert($data);
    }

    /**
     * Update
     *
     * @access public
     * @param  array   $data
     * @return boolean
     */
    public function update(array $data = array())
    {
        $values = array_merge(array_values($data), array_values($this->sumColumns), $this->conditionBuilder->getValues());
        $sql = UpdateBuilder::getInstance($this->db, $this->conditionBuilder)
            ->withTable($this->name)
            ->withColumns(array_keys($data))
            ->withSumColumns(array_keys($this->sumColumns))
            ->build();

        return $this->db->execute($sql, $values) !== false;
    }

    /**
     * Insert
     *
     * @access public
     * @param  array    $data
     * @return boolean
     */
    public function insert(array $data)
    {
        return $this->db->getStatementHandler()
            ->withSql(InsertBuilder::getInstance($this->db, $this->conditionBuilder)
                ->withTable($this->name)
                ->withColumns(array_keys($data))
                ->build()
            )
            ->withNamedParams($data)
            ->execute() !== false;
    }

    /**
     * Insert a new row and return the ID of the primary key
     *
     * @access public
     * @param  array $data
     * @return bool|int
     */
    public function persist(array $data)
    {
        if ($this->insert($data)) {
            return $this->db->getLastId();
        }

        return false;
    }

    /**
     * Remove
     *
     * @access public
     * @return boolean
     */
    public function remove()
    {
        $sql = sprintf(
            'DELETE FROM %s %s',
            $this->db->escapeIdentifier($this->name),
            $this->conditionBuilder->build()
        );

        $result = $this->db->execute($sql, $this->conditionBuilder->getValues());
        return $result->rowCount() > 0;
    }

    /**
     * Fetch all rows
     *
     * @access public
     * @return array
     */
    public function findAll()
    {
        $rq = $this->db->execute($this->buildSelectQuery(), $this->conditionBuilder->getValues());
        $results = $rq->fetchAll(PDO::FETCH_ASSOC);

        if (is_callable($this->callback) && ! empty($results)) {
            return call_user_func($this->callback, $results);
        }

        return $results;
    }

    /**
     * Find all with a single column
     *
     * @access public
     * @param  string    $column
     * @return mixed
     */
    public function findAllByColumn($column)
    {
        $this->columns = array($column);
        $rq = $this->db->execute($this->buildSelectQuery(), $this->conditionBuilder->getValues());

        return $rq->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * Fetch one row
     *
     * @access public
     * @return array|null
     */
    public function findOne()
    {
        $this->limit(1);
        $result = $this->findAll();

        return isset($result[0]) ? $result[0] : null;
    }

    /**
     * Fetch one column, first row
     *
     * @access public
     * @param  string   $column
     * @return string
     */
    public function findOneColumn($column)
    {
        $this->limit(1);
        $this->columns = array($column);

        return $this->db->execute($this->buildSelectQuery(), $this->conditionBuilder->getValues())->fetchColumn();
    }

    /**
     * Build a subquery with an alias
     *
     * @access public
     * @param  string  $sql
     * @param  string  $alias
     * @return $this
     */
    public function subquery($sql, $alias)
    {
        $this->columns[] = '('.$sql.') AS '.$this->db->escapeIdentifier($alias);
        return $this;
    }

    /**
     * Exists
     *
     * @access public
     * @return integer
     */
    public function exists()
    {
        $sql = sprintf(
            'SELECT 1 FROM %s '.implode(' ', $this->joins).$this->conditionBuilder->build(),
            $this->db->escapeIdentifier($this->name)
        );

        $rq = $this->db->execute($sql, $this->conditionBuilder->getValues());
        $result = $rq->fetchColumn();

        return $result ? true : false;
    }

    /**
     * Count
     *
     * @access public
     * @return integer
     */
    public function count()
    {
        $sql = sprintf(
            'SELECT COUNT(*) FROM %s '.implode(' ', $this->joins).$this->conditionBuilder->build().$this->sqlOrder.$this->sqlLimit.$this->sqlOffset,
            $this->db->escapeIdentifier($this->name)
        );

        $rq = $this->db->execute($sql, $this->conditionBuilder->getValues());
        $result = $rq->fetchColumn();

        return $result ? (int) $result : 0;
    }

    /**
     * Sum
     *
     * @access public
     * @param  string   $column
     * @return float
     */
    public function sum($column)
    {
        $sql = sprintf(
            'SELECT SUM(%s) FROM %s '.implode(' ', $this->joins).$this->conditionBuilder->build().$this->sqlOrder.$this->sqlLimit.$this->sqlOffset,
            $this->db->escapeIdentifier($column),
            $this->db->escapeIdentifier($this->name)
        );

        $rq = $this->db->execute($sql, $this->conditionBuilder->getValues());
        $result = $rq->fetchColumn();

        return $result ? (float) $result : 0;
    }

    /**
     * Increment column value
     *
     * @access public
     * @param  string $column
     * @param  string $value
     * @return boolean
     */
    public function increment($column, $value)
    {
        $sql = sprintf(
            'UPDATE %s SET %s=%s+%d '.$this->conditionBuilder->build(),
            $this->db->escapeIdentifier($this->name),
            $this->db->escapeIdentifier($column),
            $this->db->escapeIdentifier($column),
            $value
        );

        return $this->db->execute($sql, $this->conditionBuilder->getValues()) !== false;
    }

    /**
     * Decrement column value
     *
     * @access public
     * @param  string $column
     * @param  string $value
     * @return boolean
     */
    public function decrement($column, $value)
    {
        $sql = sprintf(
            'UPDATE %s SET %s=%s-%d '.$this->conditionBuilder->build(),
            $this->db->escapeIdentifier($this->name),
            $this->db->escapeIdentifier($column),
            $this->db->escapeIdentifier($column),
            $value
        );

        return $this->db->execute($sql, $this->conditionBuilder->getValues()) !== false;
    }

    /**
     * Left join
     *
     * @access public
     * @param  string   $table              Join table
     * @param  string   $foreign_column     Foreign key on the join table
     * @param  string   $local_column       Local column
     * @param  string   $local_table        Local table
     * @param  string   $alias              Join table alias
     * @return $this
     */
    public function join($table, $foreign_column, $local_column, $local_table = '', $alias = '')
    {
        $this->joins[] = sprintf(
            'LEFT JOIN %s ON %s=%s',
            $this->db->escapeIdentifier($table),
            $this->db->escapeIdentifier($alias ?: $table).'.'.$this->db->escapeIdentifier($foreign_column),
            $this->db->escapeIdentifier($local_table ?: $this->name).'.'.$this->db->escapeIdentifier($local_column)
        );

        return $this;
    }

    /**
     * Left join
     *
     * @access public
     * @param  string   $table1
     * @param  string   $alias1
     * @param  string   $column1
     * @param  string   $table2
     * @param  string   $column2
     * @return $this
     */
    public function left($table1, $alias1, $column1, $table2, $column2)
    {
        $this->joins[] = sprintf(
            'LEFT JOIN %s AS %s ON %s=%s',
            $this->db->escapeIdentifier($table1),
            $this->db->escapeIdentifier($alias1),
            $this->db->escapeIdentifier($alias1).'.'.$this->db->escapeIdentifier($column1),
            $this->db->escapeIdentifier($table2).'.'.$this->db->escapeIdentifier($column2)
        );

        return $this;
    }

    /**
     * Inner join
     *
     * @access public
     * @param  string   $table1
     * @param  string   $alias1
     * @param  string   $column1
     * @param  string   $table2
     * @param  string   $column2
     * @return $this
     */
    public function inner($table1, $alias1, $column1, $table2, $column2)
    {
        $this->joins[] = sprintf(
            'JOIN %s AS %s ON %s=%s',
            $this->db->escapeIdentifier($table1),
            $this->db->escapeIdentifier($alias1),
            $this->db->escapeIdentifier($alias1).'.'.$this->db->escapeIdentifier($column1),
            $this->db->escapeIdentifier($table2).'.'.$this->db->escapeIdentifier($column2)
        );

        return $this;
    }

    /**
     * Order by
     *
     * @access public
     * @param  string   $column    Column name
     * @param  string   $order     Direction ASC or DESC
     * @return $this
     */
    public function orderBy($column, $order = self::SORT_ASC)
    {
        $order = strtoupper($order);
        $order = $order === self::SORT_ASC || $order === self::SORT_DESC ? $order : self::SORT_ASC;

        if ($this->sqlOrder === '') {
            $this->sqlOrder = ' ORDER BY '.$this->db->escapeIdentifier($column).' '.$order;
        }
        else {
            $this->sqlOrder .= ', '.$this->db->escapeIdentifier($column).' '.$order;
        }

        return $this;
    }

    /**
     * Ascending sort
     *
     * @access public
     * @param  string   $column
     * @return $this
     */
    public function asc($column)
    {
        $this->orderBy($column, self::SORT_ASC);
        return $this;
    }

    /**
     * Descending sort
     *
     * @access public
     * @param  string   $column
     * @return $this
     */
    public function desc($column)
    {
        $this->orderBy($column, self::SORT_DESC);
        return $this;
    }

    /**
     * Limit
     *
     * @access public
     * @param  integer   $value
     * @return $this
     */
    public function limit($value)
    {
        if (! is_null($value)) {
            $this->sqlLimit = ' LIMIT '.(int) $value;
        }

        return $this;
    }

    /**
     * Offset
     *
     * @access public
     * @param  integer   $value
     * @return $this
     */
    public function offset($value)
    {
        if (! is_null($value)) {
            $this->sqlOffset = ' OFFSET '.(int) $value;
        }

        return $this;
    }

    /**
     * Group by
     *
     * @access public
     * @return $this
     */
    public function groupBy()
    {
        $this->groupBy = func_get_args();
        return $this;
    }

    /**
     * Custom select
     *
     * @access public
     * @param  string $select
     * @return $this
     */
    public function select($select)
    {
        $this->sqlSelect = $select;
        return $this;
    }

    /**
     * Define the columns for the select
     *
     * @access public
     * @return $this
     */
    public function columns()
    {
        $this->columns = func_get_args();
        return $this;
    }

    /**
     * Sum column
     *
     * @access public
     * @param  string  $column
     * @param  mixed   $value
     * @return $this
     */
    public function sumColumn($column, $value)
    {
        $this->sumColumns[$column] = $value;
        return $this;
    }

    /**
     * Distinct
     *
     * @access public
     * @return $this
     */
    public function distinct()
    {
        $this->columns = func_get_args();
        $this->distinct = true;
        return $this;
    }

    /**
     * Add callback to alter the resultset
     *
     * @access public
     * @param  Closure|array  $callback
     * @return $this
     */
    public function callback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Build a select query
     *
     * @access public
     * @return string
     */
    public function buildSelectQuery()
    {
        if (empty($this->sqlSelect)) {
            $this->columns = $this->db->escapeIdentifierList($this->columns);
            $this->sqlSelect = ($this->distinct ? 'DISTINCT ' : '').(empty($this->columns) ? '*' : implode(', ', $this->columns));
        }

        $this->groupBy = $this->db->escapeIdentifierList($this->groupBy);

        return trim(sprintf(
            'SELECT %s FROM %s %s %s %s %s %s %s',
            $this->sqlSelect,
            $this->db->escapeIdentifier($this->name),
            implode(' ', $this->joins),
            $this->conditionBuilder->build(),
            empty($this->groupBy) ? '' : 'GROUP BY '.implode(', ', $this->groupBy),
            $this->sqlOrder,
            $this->sqlLimit,
            $this->sqlOffset
        ));
    }

    /**
     * Magic method for sql conditions
     *
     * @access public
     * @param  string   $name
     * @param  array    $arguments
     * @return $this
     */
    public function __call($name, array $arguments)
    {
        call_user_func_array(array($this->conditionBuilder, $name), $arguments);
        return $this;
    }
}
