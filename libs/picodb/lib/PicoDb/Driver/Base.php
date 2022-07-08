<?php

namespace PicoDb\Driver;

use PDO;
use LogicException;
use PDOException;

/**
 * Base Driver class
 *
 * @package PicoDb\Driver
 * @author  Frederic Guillot
 */
abstract class Base
{
    /**
     * List of required settings options
     *
     * @access protected
     * @var array
     */
    protected $requiredAttributes = array();

    /**
     * PDO connection
     *
     * @access protected
     * @var PDO
     */
    protected $pdo = null;

    /**
     * use TOP or LIMIT for returning a subset of rows
     *
     * @access public
     * @var bool
     */
    public bool $useTop;

    /**
     * Create a new PDO connection
     *
     * @abstract
     * @access public
     * @param  array   $settings
     */
    abstract public function createConnection(array $settings);

    /**
     * Enable foreign keys
     *
     * @abstract
     * @access public
     */
    abstract public function enableForeignKeys();

    /**
     * Disable foreign keys
     *
     * @abstract
     * @access public
     */
    abstract public function disableForeignKeys();

    /**
     * Return true if the error code is a duplicate key
     *
     * @abstract
     * @access public
     * @param  integer  $code
     * @return boolean
     */
    abstract public function isDuplicateKeyError($code);

    /**
     * Escape identifier
     *
     * @abstract
     * @access public
     * @param  string  $identifier
     * @return string
     */
    abstract public function escape($identifier);

    /**
     * Get non standard operator
     *
     * @abstract
     * @access public
     * @param  string  $operator
     * @return string
     */
    abstract public function getOperator($operator);

    /**
     * Get last inserted id
     *
     * @abstract
     * @access public
     * @return integer
     */
    abstract public function getLastId();

    /**
     * Get current schema version
     *
     * @abstract
     * @access public
     * @return integer
     */
    abstract public function getSchemaVersion();

    /**
     * Set current schema version
     *
     * @abstract
     * @access public
     * @param  integer  $version
     */
    abstract public function setSchemaVersion($version);

    /**
     * Constructor
     *
     * @access public
     * @param  array   $settings
     */
    public function __construct(array $settings)
    {
        foreach ($this->requiredAttributes as $attribute) {
            if (! isset($settings[$attribute])) {
                throw new LogicException('This configuration parameter is missing: "'.$attribute.'"');
            }
        }

        $this->createConnection($settings);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->useTop = false;
    }

    /**
     * Get the PDO connection
     *
     * @access public
     * @return PDO
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    /**
     * Release the PDO connection
     *
     * @access public
     */
    public function closeConnection()
    {
        $this->pdo = null;
    }

    /**
     * Upsert for a key/value variable
     *
     * @access public
     * @param  string  $table
     * @param  string  $keyColumn
     * @param  string  $valueColumn
     * @param  array   $dictionary
     * @return bool    False on failure
     */
    public function upsert($table, $keyColumn, $valueColumn, array $dictionary)
    {
        try {
            $this->pdo->beginTransaction();

            foreach ($dictionary as $key => $value) {

                $rq = $this->pdo->prepare('SELECT 1 FROM '.$this->escape($table).' WHERE '.$this->escape($keyColumn).'=?');
                $rq->execute(array($key));

                if ($rq->fetchColumn()) {
                    $rq = $this->pdo->prepare('UPDATE '.$this->escape($table).' SET '.$this->escape($valueColumn).'=? WHERE '.$this->escape($keyColumn).'=?');
                    $rq->execute(array($value, $key));
                }
                else {
                    $rq = $this->pdo->prepare('INSERT INTO '.$this->escape($table).' ('.$this->escape($keyColumn).', '.$this->escape($valueColumn).') VALUES (?, ?)');
                    $rq->execute(array($key, $value));
                }
            }

            $this->pdo->commit();

            return true;
        }
        catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Run EXPLAIN command
     *
     * @access public
     * @param  string $sql
     * @param  array  $values
     * @return array
     */
    public function explain($sql, array $values)
    {
        return $this->getConnection()->query('EXPLAIN '.$this->getSqlFromPreparedStatement($sql, $values))->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Replace placeholder with values in prepared statement
     *
     * @access protected
     * @param  string $sql
     * @param  array  $values
     * @return string
     */
    protected function getSqlFromPreparedStatement($sql, array $values)
    {
        foreach ($values as $value) {
            $sql = substr_replace($sql, "'$value'", strpos($sql, '?'), 1);
        }

        return $sql;
    }

    /**
     * Get database version
     *
     * @access public
     * @return array
     */
    public function getDatabaseVersion()
    {
        return $this->getConnection()->query('SELECT VERSION()')->fetchColumn();
    }

}
