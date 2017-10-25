<?php

namespace PicoDb\Driver;

use PDO;
use PDOException;

/**
 * Sqlite Driver
 *
 * @package PicoDb\Driver
 * @author  Frederic Guillot
 */
class Sqlite extends Base
{
    /**
     * List of required settings options
     *
     * @access protected
     * @var array
     */
    protected $requiredAttributes = array('filename');

    /**
     * Create a new PDO connection
     *
     * @access public
     * @param  array   $settings
     */
    public function createConnection(array $settings)
    {
        $this->pdo = new PDO('sqlite:'.$settings['filename']);
        $this->enableForeignKeys();
    }

    /**
     * Enable foreign keys
     *
     * @access public
     */
    public function enableForeignKeys()
    {
        $this->pdo->exec('PRAGMA foreign_keys = ON');
    }

    /**
     * Disable foreign keys
     *
     * @access public
     */
    public function disableForeignKeys()
    {
        $this->pdo->exec('PRAGMA foreign_keys = OFF');
    }

    /**
     * Return true if the error code is a duplicate key
     *
     * @access public
     * @param  integer  $code
     * @return boolean
     */
    public function isDuplicateKeyError($code)
    {
        return $code == 23000;
    }

    /**
     * Escape identifier
     *
     * @access public
     * @param  string  $identifier
     * @return string
     */
    public function escape($identifier)
    {
        return '"'.$identifier.'"';
    }

    /**
     * Get non standard operator
     *
     * @access public
     * @param  string  $operator
     * @return string
     */
    public function getOperator($operator)
    {
        if ($operator === 'LIKE' || $operator === 'ILIKE') {
            return 'LIKE';
        }

        return '';
    }

    /**
     * Get last inserted id
     *
     * @access public
     * @return integer
     */
    public function getLastId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Get current schema version
     *
     * @access public
     * @return integer
     */
    public function getSchemaVersion()
    {
        $rq = $this->pdo->prepare('PRAGMA user_version');
        $rq->execute();

        return (int) $rq->fetchColumn();
    }

    /**
     * Set current schema version
     *
     * @access public
     * @param  integer  $version
     */
    public function setSchemaVersion($version)
    {
        $this->pdo->exec('PRAGMA user_version='.$version);
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

                $sql = sprintf(
                    'INSERT OR REPLACE INTO %s (%s, %s) VALUES (?, ?)',
                    $this->escape($table),
                    $this->escape($keyColumn),
                    $this->escape($valueColumn)
                );

                $rq = $this->pdo->prepare($sql);
                $rq->execute(array($key, $value));
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
        return $this->getConnection()->query('EXPLAIN QUERY PLAN '.$this->getSqlFromPreparedStatement($sql, $values))->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get database version
     *
     * @access public
     * @return array
     */
    public function getDatabaseVersion()
    {
        return $this->getConnection()->query('SELECT sqlite_version()')->fetchColumn();
    }
}
