<?php

namespace PicoDb;

use Closure;
use PDOException;
use LogicException;
use PicoDb\SQLException;
use PicoDb\Driver\Mssql;
use PicoDb\Driver\Sqlite;
use PicoDb\Driver\Mysql;
use PicoDb\Driver\Postgres;

/**
 * Database
 *
 * @package PicoDb
 * @author  Frederic Guillot
 */
class Database
{
    /**
     * Database instances
     *
     * @static
     * @access private
     * @var array
     */
    private static $instances = array();

    /**
     * Statement object
     *
     * @access protected
     * @var StatementHandler
     */
    protected $statementHandler;

    /**
     * Queries logs
     *
     * @access private
     * @var array
     */
    private $logs = array();

    /**
     * Driver instance
     *
     * @access private
     */
    private $driver;

    /**
     * Initialize the driver
     *
     * @access public
     * @param  array   $settings
     */
    public function __construct(array $settings = array())
    {
        $this->driver = DriverFactory::getDriver($settings);
        $this->statementHandler = new StatementHandler($this);
    }

    /**
     * Destructor
     *
     * @access public
     */
    public function __destruct()
    {
        $this->closeConnection();
    }

    /**
     * Register a new database instance
     *
     * @static
     * @access public
     * @param  string    $name        Instance name
     * @param  Closure   $callback    Callback
     */
    public static function setInstance($name, Closure $callback)
    {
        self::$instances[$name] = $callback;
    }

    /**
     * Get a database instance
     *
     * @static
     * @access public
     * @param  string    $name   Instance name
     * @return Database
     */
    public static function getInstance($name)
    {
        if (! isset(self::$instances[$name])) {
            throw new LogicException('No database instance created with that name');
        }

        if (is_callable(self::$instances[$name])) {
            self::$instances[$name] = call_user_func(self::$instances[$name]);
        }

        return self::$instances[$name];
    }

    /**
     * Add a log message
     *
     * @access public
     * @param  mixed $message
     * @return Database
     */
    public function setLogMessage($message)
    {
        $this->logs[] = is_array($message) ? var_export($message, true) : $message;
        return $this;
    }

    /**
     * Add many log messages
     *
     * @access public
     * @param  array $messages
     * @return Database
     */
    public function setLogMessages(array $messages)
    {
        foreach ($messages as $message) {
            $this->setLogMessage($message);
        }

        return $this;
    }

    /**
     * Get all queries logs
     *
     * @access public
     * @return array
     */
    public function getLogMessages()
    {
        return $this->logs;
    }

    /**
     * Get the PDO connection
     *
     * @access public
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->driver->getConnection();
    }

    /**
     * Get the Driver instance
     *
     * @access public
     * @return Mssql|Sqlite|Postgres|Mysql
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Get the last inserted id
     *
     * @access public
     * @return integer
     */
    public function getLastId()
    {
        return (int) $this->driver->getLastId();
    }

    /**
     * Get statement object
     *
     * @access public
     * @return StatementHandler
     */
    public function getStatementHandler()
    {
        return $this->statementHandler;
    }

    /**
     * Release the PDO connection
     *
     * @access public
     */
    public function closeConnection()
    {
        $this->driver->closeConnection();
    }

    /**
     * Escape an identifier (column, table name...)
     *
     * @access public
     * @param  string    $value    Value
     * @param  string    $table    Table name
     * @return string
     */
    public function escapeIdentifier($value, $table = '')
    {
        // Do not escape custom query
        if (strpos($value, '.') !== false || strpos($value, ' ') !== false) {
            return $value;
        }

        // Avoid potential SQL injection
        if (preg_match('/^[a-z0-9_]+$/', $value) === 0) {
            throw new SQLException('Invalid identifier: '.$value);
        }

        if (! empty($table)) {
            return $this->driver->escape($table).'.'.$this->driver->escape($value);
        }

        return $this->driver->escape($value);
    }

    /**
     * Escape an identifier list
     *
     * @access public
     * @param  array     $identifiers  List of identifiers
     * @param  string    $table        Table name
     * @return string[]
     */
    public function escapeIdentifierList(array $identifiers, $table = '')
    {
        foreach ($identifiers as $key => $value) {
            $identifiers[$key] = $this->escapeIdentifier($value, $table);
        }

        return $identifiers;
    }

    /**
     * Execute a prepared statement
     *
     * Note: returns false on duplicate keys instead of SQLException
     *
     * @access public
     * @param  string    $sql      SQL query
     * @param  array     $values   Values
     * @return \PDOStatement|false
     */
    public function execute($sql, array $values = array())
    {
        return $this->statementHandler
            ->withSql($sql)
            ->withPositionalParams($values)
            ->execute();
    }

    /**
     * Run a transaction
     *
     * @access public
     * @param  Closure    $callback     Callback
     * @return mixed
     */
    public function transaction(Closure $callback)
    {
        try {

            $this->startTransaction();
            $result = $callback($this);
            $this->closeTransaction();

            return $result === null ? true : $result;
        } catch (PDOException $e) {
            return $this->statementHandler->handleSqlError($e);
        }
    }

    /**
     * Begin a transaction
     *
     * @access public
     */
    public function startTransaction()
    {
        if (! $this->getConnection()->inTransaction()) {
            $this->getConnection()->beginTransaction();
        }
    }

    /**
     * Commit a transaction
     *
     * @access public
     */
    public function closeTransaction()
    {
        if ($this->getConnection()->inTransaction()) {
            $this->getConnection()->commit();
        }
    }

    /**
     * Rollback a transaction
     *
     * @access public
     */
    public function cancelTransaction()
    {
        if ($this->getConnection()->inTransaction()) {
            $this->getConnection()->rollBack();
        }
    }

    /**
     * Get a table object
     *
     * @access public
     * @param  string $table
     * @return Table
     */
    public function table($table)
    {
        return new Table($this, $table);
    }

    /**
     * Get a hashtable object
     *
     * @access public
     * @param  string $table
     * @return Hashtable
     */
    public function hashtable($table)
    {
        return new Hashtable($this, $table);
    }

    /**
     * Get a LOB object
     *
     * @access public
     * @param  string $table
     * @return LargeObject
     */
    public function largeObject($table)
    {
        return new LargeObject($this, $table);
    }

    /**
     * Get a schema object
     *
     * @access public
     * @param  string $namespace
     * @return Schema
     */
    public function schema($namespace = null)
    {
        $schema = new Schema($this);

        if ($namespace !== null) {
            $schema->setNamespace($namespace);
        }

        return $schema;
    }
}
