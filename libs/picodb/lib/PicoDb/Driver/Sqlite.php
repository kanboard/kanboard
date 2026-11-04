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
     * Compiled regular expressions for the LIKE function
     *
     * @access private
     * @var array
     */
    private $likeRegexCache = array();

    /**
     * Create a new PDO connection
     *
     * @access public
     * @param  array   $settings
     */
    public function createConnection(array $settings)
    {
        $options = [];

        // Set a default timeout of 30 seconds to reduce "database is locked" errors.
        $options[PDO::ATTR_TIMEOUT] = (! empty($settings['timeout'])) ? $settings['timeout'] : 30;

        // The Pdo\Sqlite driver class was introduced in PHP 8.4.0.
        $dsn = 'sqlite:'.$settings['filename'];
        $this->pdo = class_exists('Pdo\\Sqlite') ? new \Pdo\Sqlite($dsn, null, null, $options) : new PDO($dsn, null, null, $options);

        // Enabling WAL mode by default should also reduce the "database is locked" errors.
        // Official docs: https://sqlite.org/wal.html
        if (isset($settings['wal_mode']) && $settings['wal_mode'] === true) {
            $this->pdo->exec('PRAGMA journal_mode=wal');
            $this->pdo->exec('PRAGMA wal_autocheckpoint = 0');
            $this->pdo->exec('PRAGMA synchronous=NORMAL');
        }

        $this->enableForeignKeys();
        $this->registerFunctions();
    }

    /**
     * Register custom SQL functions
     *
     * The built-in LIKE operator of Sqlite is case-insensitive only for ASCII characters.
     *
     * @access protected
     */
    protected function registerFunctions()
    {
        // PHP 8.5 deprecates PDO::sqliteCreateFunction() in favor of Pdo\Sqlite::createFunction().
        $method = method_exists($this->pdo, 'createFunction') ? 'createFunction' : 'sqliteCreateFunction';
        $callback = array($this, 'like');

        $this->pdo->$method('like', $callback, 2);
        $this->pdo->$method('like', $callback, 3);
    }

    /**
     * Unicode case-insensitive implementation of the LIKE operator
     *
     * @access public
     * @param  string|null  $pattern
     * @param  string|null  $value
     * @param  string|null  $escape
     * @return integer|null
     */
    public function like($pattern, $value, $escape = null)
    {
        if ($pattern === null || $value === null) {
            return null;
        }

        $key = $escape."\0".$pattern;

        if (! isset($this->likeRegexCache[$key])) {
            if (count($this->likeRegexCache) > 100) {
                $this->likeRegexCache = array();
            }

            $this->likeRegexCache[$key] = $this->buildLikeRegex((string) $pattern, $escape === null ? null : (string) $escape);
        }

        return preg_match($this->likeRegexCache[$key], (string) $value) === 1 ? 1 : 0;
    }

    /**
     * Convert a LIKE pattern to a regular expression
     *
     * @access private
     * @param  string       $pattern
     * @param  string|null  $escape
     * @return string
     */
    private function buildLikeRegex($pattern, $escape)
    {
        $chars = preg_split('//u', $pattern, -1, PREG_SPLIT_NO_EMPTY) ?: array();
        $regex = '';

        for ($i = 0, $length = count($chars); $i < $length; $i++) {
            $char = $chars[$i];

            if ($char === $escape && $i + 1 < $length) {
                $regex .= preg_quote($chars[++$i], '/');
            } elseif ($char === '%') {
                $regex .= '.*';
            } elseif ($char === '_') {
                $regex .= '.';
            } else {
                $regex .= preg_quote($char, '/');
            }
        }

        return '/^'.$regex.'$/uis';
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
     * @return string
     */
    public function getDatabaseVersion()
    {
        return $this->getConnection()->query('SELECT sqlite_version()')->fetchColumn();
    }
}
