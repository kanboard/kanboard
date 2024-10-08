<?php

namespace PicoDb;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Statement Handler
 *
 * @package PicoDb
 * @author  Frederic Guillot
 */
class StatementHandler
{
    /**
     * Database instance
     *
     * @access protected
     * @var Database
     */
    protected $db = null;

    /**
     * Flag to calculate query time
     *
     * @access protected
     * @var boolean
     */
    protected $stopwatch = false;

    /**
     * Start time
     *
     * @access protected
     * @var float
     */
    protected $startTime = 0;

    /**
     * Execution time of all queries
     *
     * @access protected
     * @var float
     */
    protected $executionTime = 0;

    /**
     * Flag to log generated SQL queries
     *
     * @access protected
     * @var boolean
     */
    protected $logQueries = false;

    /**
     * Run explain command on each query
     *
     * @access protected
     * @var boolean
     */
    protected $explain = false;

    /**
     * Number of SQL queries executed
     *
     * @access protected
     * @var integer
     */
    protected $nbQueries = 0;

    /**
     * SQL query
     *
     * @access protected
     * @var string
     */
    protected $sql = '';

    /**
     * Positional SQL parameters
     *
     * @access protected
     * @var array
     */
    protected $positionalParams = array();

    /**
     * Named SQL parameters
     *
     * @access protected
     * @var array
     */
    protected $namedParams = array();

    /**
     * Flag to use named params
     *
     * @access protected
     * @var boolean
     */
    protected $useNamedParams = false;

    /**
     * LOB params
     *
     * @access protected
     * @var array
     */
    protected $lobParams = array();

    /**
     * Constructor
     *
     * @access public
     * @param  Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Enable query logging
     *
     * @access public
     * @return $this
     */
    public function withLogging()
    {
        $this->logQueries = true;
        return $this;
    }

    /**
     * Record query execution time
     *
     * @access public
     * @return $this
     */
    public function withStopWatch()
    {
        $this->stopwatch = true;
        return $this;
    }

    /**
     * Execute explain command on query
     *
     * @access public
     * @return $this
     */
    public function withExplain()
    {
        $this->explain = true;
        return $this;
    }

    /**
     * Set SQL query
     *
     * @access public
     * @param  string  $sql
     * @return $this
     */
    public function withSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * Set positional parameters
     *
     * @access public
     * @param  array $params
     * @return $this
     */
    public function withPositionalParams(array $params)
    {
        $this->positionalParams = $params;
        return $this;
    }

    /**
     * Set named parameters
     *
     * @access public
     * @param  array $params
     * @return $this
     */
    public function withNamedParams(array $params)
    {
        $this->namedParams = $params;
        $this->useNamedParams = true;
        return $this;
    }

    /**
     * Bind large object parameter
     *
     * @access public
     * @param $name
     * @param $fp
     * @return $this
     */
    public function withLobParam($name, &$fp)
    {
        $this->lobParams[$name] =& $fp;
        return $this;
    }

    /**
     * Get number of queries executed
     *
     * @access public
     * @return int
     */
    public function getNbQueries()
    {
        return $this->nbQueries;
    }

    /**
     * Execute a prepared statement
     *
     * Note: returns false on duplicate keys instead of SQLException
     *
     * @access public
     * @return PDOStatement|false
     */
    public function execute()
    {
        try {
            $this->beforeExecute();

            $pdoStatement = $this->db->getConnection()->prepare($this->sql);
            $this->bindParams($pdoStatement);
            $pdoStatement->execute();

            $this->afterExecute();
            return $pdoStatement;
        } catch (PDOException $e) {
            return $this->handleSqlError($e);
        }
    }

    /**
     * Bind parameters to PDOStatement
     *
     * @access protected
     * @param PDOStatement $pdoStatement
     */
    protected function bindParams(PDOStatement $pdoStatement)
    {
        $i = 1;

        foreach ($this->lobParams as $name => $variable) {
            if (! $this->useNamedParams) {
                $parameter = $i;
                $i++;
            } else {
                $parameter = $name;
            }

            $pdoStatement->bindParam($parameter, $variable, PDO::PARAM_LOB);
        }

        foreach ($this->positionalParams as $value) {
            $pdoStatement->bindValue($i, $value, PDO::PARAM_STR);
            $this->db->setLogMessage("param[$i]: '$value'");
            $i++;
        }

        foreach ($this->namedParams as $name => $value) {
            $pdoStatement->bindValue($name, $value, PDO::PARAM_STR);
            $this->db->setLogMessage("param[$name]: '$value'");
        }
    }

    /**
     * Method executed before query execution
     *
     * @access protected
     */
    protected function beforeExecute()
    {
        if ($this->logQueries) {
            $this->db->setLogMessage($this->sql);
        }

        if ($this->stopwatch) {
            $this->startTime = microtime(true);
        }
    }

    /**
     * Method executed after query execution
     *
     * @access protected
     */
    protected function afterExecute()
    {
        if ($this->stopwatch) {
            $duration = microtime(true) - $this->startTime;
            $this->executionTime += $duration;
            $this->db->setLogMessage('query_duration='.$duration);
            $this->db->setLogMessage('total_execution_time='.$this->executionTime);
        }

        if ($this->explain) {
            $this->db->setLogMessages($this->db->getDriver()->explain($this->sql, $this->positionalParams));
        }

        $this->nbQueries++;
        $this->cleanup();
    }

    /**
     * Reset internal properties after execution
     * The same object instance is used
     *
     * @access protected
     */
    protected function cleanup()
    {
        $this->sql = '';
        $this->useNamedParams = false;
        $this->positionalParams = array();
        $this->namedParams = array();
        $this->lobParams = array();
    }

    /**
     * Handle PDOException
     *
     * @access public
     * @param  PDOException $e
     * @return bool
     * @throws SQLException
     */
    public function handleSqlError(PDOException $e)
    {
        $this->cleanup();
        $this->db->cancelTransaction();
        $this->db->setLogMessage($e->getMessage());

        if ($this->db->getDriver()->isDuplicateKeyError($e->getCode())) {
            return false;
        }

        throw new SQLException('SQL Error['.$e->getCode().']: '.$e->getMessage());
    }
}
