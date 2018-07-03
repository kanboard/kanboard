<?php

namespace PicoDb\Driver;

use PDO;
use PDOException;

/**
 * Mysql Driver
 *
 * @package PicoDb\Driver
 * @author  Frederic Guillot
 */
class Mysql extends Base
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
        $this->pdo = new PDO(
            $this->buildDsn($settings),
            $settings['username'],
            $settings['password'],
            $this->buildOptions($settings)
        );

        if (isset($settings['schema_table'])) {
            $this->schemaTable = $settings['schema_table'];
        }
    }

    /**
     * Build connection DSN
     *
     * @access protected
     * @param  array $settings
     * @return string
     */
    protected function buildDsn(array $settings)
    {
        $dsn = 'mysql:host='.$settings['hostname'].';dbname='.$settings['database'];

        if (! empty($settings['port'])) {
            $dsn .= ';port='.$settings['port'];
        }

        return $dsn;
    }

    /**
     * Build connection options
     *
     * @access protected
     * @param  array $settings
     * @return array
     */
    protected function buildOptions(array $settings)
    {
        $charset = empty($settings['charset']) ? 'utf8' : $settings['charset'];
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode = STRICT_ALL_TABLES, NAMES ' . $charset,
        );

        if (! empty($settings['ssl_key'])) {
            $options[PDO::MYSQL_ATTR_SSL_KEY] = $settings['ssl_key'];
        }

        if (! empty($settings['ssl_cert'])) {
            $options[PDO::MYSQL_ATTR_SSL_CERT] = $settings['ssl_cert'];
        }

        if (! empty($settings['ssl_ca'])) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $settings['ssl_ca'];
        }

        if (! empty($settings['persistent'])) {
            $options[PDO::ATTR_PERSISTENT] = $settings['persistent'];
        }

        if (! empty($settings['timeout'])) {
            $options[PDO::ATTR_TIMEOUT] = $settings['timeout'];
        }

        if (isset($settings['verify_server_cert'])) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $settings['verify_server_cert'];
        }

        if (! empty($settings['case'])) {
            $options[PDO::ATTR_CASE] = $settings['case'];
        }

        return $options;
    }

    /**
     * Enable foreign keys
     *
     * @access public
     */
    public function enableForeignKeys()
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Disable foreign keys
     *
     * @access public
     */
    public function disableForeignKeys()
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS=0');
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
        return '`'.$identifier.'`';
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
            return 'LIKE BINARY';
        }
        else if ($operator === 'ILIKE') {
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
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS `".$this->schemaTable."` (`version` INT DEFAULT '0') ENGINE=InnoDB CHARSET=utf8");

        $rq = $this->pdo->prepare('SELECT `version` FROM `'.$this->schemaTable.'`');
        $rq->execute();
        $result = $rq->fetchColumn();

        if ($result !== false) {
            return (int) $result;
        }
        else {
            $this->pdo->exec('INSERT INTO `'.$this->schemaTable.'` VALUES(0)');
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
        $rq = $this->pdo->prepare('UPDATE `'.$this->schemaTable.'` SET `version`=?');
        $rq->execute(array($version));
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

            $sql = sprintf(
                'REPLACE INTO %s (%s, %s) VALUES %s',
                $this->escape($table),
                $this->escape($keyColumn),
                $this->escape($valueColumn),
                implode(', ', array_fill(0, count($dictionary), '(?, ?)'))
            );

            $values = array();

            foreach ($dictionary as $key => $value) {
                $values[] = $key;
                $values[] = $value;
            }

            $rq = $this->pdo->prepare($sql);
            $rq->execute($values);

            return true;
        }
        catch (PDOException $e) {
            return false;
        }
    }
}
