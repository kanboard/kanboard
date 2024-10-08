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
    );

    /**
     * Constructor
     *
     * @access public
     * @param  array   $settings
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->useFetch = true;
        $this->useOffsetRows = true;
        $this->useTop = true;
    }

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
        $dsn = $settings['driver'] . ':';

        // exactly one of hostname/DSN needed, port is optional
        if ($settings['driver'] == 'odbc') {
            $dsn .= $settings['odbc-dsn'];
        } else {
            if ($settings['driver'] == 'dblib') {
                $dsn .= 'host=' . $settings['hostname'];
            } elseif ($settings['driver'] == 'sqlsrv') {
                $dsn .= 'Server=' . $settings['hostname'];
            }
            if (! empty($settings['port'])) {
                $dsn .= ',' . $settings['port'];
            }
        }

        if (! empty($settings['database'])) {
            if ($settings['driver'] == 'dblib') {
                $dsn .= ';dbname=' . $settings['database'];
            } elseif ($settings['driver'] == 'sqlsrv') {
                $dsn .= ';Database=' . $settings['database'];
            }
        }


        if (! empty($settings['appname'])) {
            if ($settings['driver'] == 'dblib') {
                $dsn .= ';appname=' . $settings['appname'];
            } elseif ($settings['driver'] == 'sqlsrv') {
                $dsn .= ';APP=' . $settings['appname'];
            }
        }

        // create PDO object
        if (! empty($settings['username'])) {
            $this->pdo = new PDO($dsn, $settings['username'], $settings['password']);
        } else {
            $this->pdo = new PDO($dsn);
        }

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
        $this->pdo->exec('EXEC sp_MSforeachtable @command1="ALTER TABLE ? CHECK CONSTRAINT ALL";');
    }

    /**
     * Disable foreign keys
     *
     * @access public
     */
    public function disableForeignKeys()
    {
        $this->pdo->exec('EXEC sp_MSforeachtable @command1="ALTER TABLE ? NOCHECK CONSTRAINT ALL";');
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
        # 2601: Cannot insert duplicate key row in object '%.*ls' with unique index '%.*ls'.
        # 2627: Violation of %ls constraint '%.*ls'. Cannot insert duplicate key in object '%.*ls'.
        # 23000: Integrity constraint violation
        return array_search($code, ['2601','2627','23000']) !== false;
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
        return '['.str_replace("]","]]",$identifier).']';
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
        try {
            $rq = $this->pdo->prepare('SELECT @@IDENTITY');
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
        $this->pdo->exec("
            IF OBJECT_ID(N'dbo.".$this->schemaTable."', N'U') IS NULL
              CREATE TABLE dbo.".$this->schemaTable." (
                version INT DEFAULT '0'
              );
        ");

        $rq = $this->pdo->prepare('SELECT version FROM dbo.'.$this->schemaTable.'');
        $rq->execute();
        $result = $rq->fetchColumn();

        if ($result !== false) {
            return (int) $result;
        }
        else {
            $this->pdo->exec('INSERT INTO dbo.'.$this->schemaTable.' (version) VALUES(0)');
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

    /**
     * Get database version
     *
     * @access public
     * @return array
     */
    public function getDatabaseVersion()
    {
        return $this->getConnection()->query('SELECT @@VERSION;')->fetchColumn();
    }

}
