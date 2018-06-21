<?php

namespace PicoDb\Driver;

use PDO;
use PDOException;

/**
 * Postgres Driver
 *
 * @package PicoDb\Driver
 * @author  Frederic Guillot
 */
class Postgres extends Base
{
    /**
     * List of required settings options
     *
     * @access protected
     * @var array
     */
    protected $requiredAttributes = array(
        'database',
    );

    /**
     * Table to store the schema version
     *
     * @access private
     * @var array
     */
    private $schemaTable = 'schema_version';

    /**
     * Create a new PDO connection
     *
     * @access public
     * @param  array   $settings
     */
    public function createConnection(array $settings)
    {
        $dsn = 'pgsql:dbname='.$settings['database'];
        $username = null;
        $password = null;
        $options = array();

        if (! empty($settings['username'])) {
            $username = $settings['username'];
        }

        if (! empty($settings['password'])) {
            $password = $settings['password'];
        }

        if (! empty($settings['hostname'])) {
            $dsn .= ';host='.$settings['hostname'];
        }

        if (! empty($settings['port'])) {
            $dsn .= ';port='.$settings['port'];
        }

        if (! empty($settings['timeout'])) {
            $options[PDO::ATTR_TIMEOUT] = $settings['timeout'];
        }

        $this->pdo = new PDO($dsn, $username, $password, $options);

        if (isset($settings['schema_table'])) {
            $this->schemaTable = $settings['schema_table'];
        }
    }

    /**
     * Enable foreign keys
     *
     * @access public
     */
    public function enableForeignKeys()
    {
    }

    /**
     * Disable foreign keys
     *
     * @access public
     */
    public function disableForeignKeys()
    {
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
        return $code == 23505 || $code == 23503;
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
        if ($operator === 'LIKE') {
            return 'LIKE';
        }
        else if ($operator === 'ILIKE') {
            return 'ILIKE';
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
        try {
            $rq = $this->pdo->prepare('SELECT LASTVAL()');
            $rq->execute();

            return $rq->fetchColumn();
        }
        catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Get current schema version
     *
     * @access public
     * @return integer
     */
    public function getSchemaVersion()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS ".$this->schemaTable." (version INTEGER DEFAULT 0)");

        $rq = $this->pdo->prepare('SELECT "version" FROM "'.$this->schemaTable.'"');
        $rq->execute();
        $result = $rq->fetchColumn();

        if ($result !== false) {
            return (int) $result;
        }
        else {
            $this->pdo->exec('INSERT INTO '.$this->schemaTable.' VALUES(0)');
        }

        return 0;
    }

    /**
     * Set current schema version
     *
     * @access public
     * @param  integer  $version
     */
    public function setSchemaVersion($version)
    {
        $rq = $this->pdo->prepare('UPDATE '.$this->schemaTable.' SET version=?');
        $rq->execute(array($version));
    }

    /**
     * Run EXPLAIN command
     *
     * @param  string $sql
     * @param  array  $values
     * @return array
     */
    public function explain($sql, array $values)
    {
        return $this->getConnection()->query('EXPLAIN (FORMAT YAML) '.$this->getSqlFromPreparedStatement($sql, $values))->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get database version
     *
     * @access public
     * @return array
     */
    public function getDatabaseVersion()
    {
        return $this->getConnection()->query('SHOW server_version')->fetchColumn();
    }
}
