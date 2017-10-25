<?php

namespace PicoDb\Driver;

use PDO;

/**
 * Microsoft SQL Server Driver
 *
 * @package PicoDb\Driver
 * @author  Algy Taylor <thomas.taylor@cmft.nhs.uk>
 */
class Mssql extends Base
{
    /**
     * List of required settings options
     *
     * @access protected
     * @var array
     */
    protected $requiredAttributes = array(
        'hostname',
        'username',
        'password',
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
        $dsn = 'sqlsrv:Server=' . $settings['hostname'] . ';Database=' . $settings['database'];

        if (! empty($settings['port'])) {
            $dsn .= ';port=' . $settings['port'];
        }

        $this->pdo = new PDO($dsn, $settings['username'], $settings['password']);

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
        $this->pdo->exec('EXEC sp_MSforeachtable @command1="ALTER TABLE ? CHECK CONSTRAINT ALL"; GO;');
    }

    /**
     * Disable foreign keys
     *
     * @access public
     */
    public function disableForeignKeys()
    {
        $this->pdo->exec('EXEC sp_MSforeachtable @command1="ALTER TABLE ? NOCHECK CONSTRAINT ALL"; GO;');
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
        return $code == 2601;
    }

    /**
     * Escape identifier
     *
     * https://msdn.microsoft.com/en-us/library/ms175874.aspx
     *
     * @access public
     * @param  string  $identifier
     * @return string
     */
    public function escape($identifier)
    {
        return '['.$identifier.']';
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
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS [".$this->schemaTable."] ([version] INT DEFAULT '0')");

        $rq = $this->pdo->prepare('SELECT [version] FROM ['.$this->schemaTable.']');
        $rq->execute();
        $result = $rq->fetchColumn();

        if ($result !== false) {
            return (int) $result;
        }
        else {
            $this->pdo->exec('INSERT INTO ['.$this->schemaTable.'] VALUES(0)');
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
        $rq = $this->pdo->prepare('UPDATE ['.$this->schemaTable.'] SET [version]=?');
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
        $this->getConnection()->exec('SET SHOWPLAN_ALL ON');
        return $this->getConnection()->query($this->getSqlFromPreparedStatement($sql, $values))->fetchAll(PDO::FETCH_ASSOC);
    }
}
