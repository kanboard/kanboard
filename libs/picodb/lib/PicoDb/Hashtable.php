<?php

namespace PicoDb;

use PDO;

/**
 * HashTable (key/value)
 *
 * @package PicoDb
 * @author  Frederic Guillot
 * @author  Mathias Kresin
 */
class Hashtable extends Table
{
    /**
     * Column for the key
     *
     * @access private
     * @var    string
     */
    private $keyColumn = 'key';

    /**
     * Column for the value
     *
     * @access private
     * @var    string
     */
    private $valueColumn = 'value';

    /**
     * Set the key column
     *
     * @access public
     * @param  string  $column
     * @return $this
     */
    public function columnKey($column)
    {
        $this->keyColumn = $column;
        return $this;
    }

    /**
     * Set the value column
     *
     * @access public
     * @param  string  $column
     * @return $this
     */
    public function columnValue($column)
    {
        $this->valueColumn = $column;
        return $this;
    }

    /**
     * Insert or update
     *
     * @access public
     * @param  array    $hashmap
     * @return boolean
     */
    public function put(array $hashmap)
    {
        return $this->db->getDriver()->upsert($this->getName(), $this->keyColumn, $this->valueColumn, $hashmap);
    }

    /**
     * Hashmap result [ [column1 => column2], [], ...]
     *
     * @access public
     * @return array
     */
    public function get()
    {
        $hashmap = array();

        // setup where condition
        if (func_num_args() > 0) {
            $this->in($this->keyColumn, func_get_args());
        }

        // setup to select columns in case that there are more than two
        $this->columns($this->keyColumn, $this->valueColumn);

        $rq = $this->db->execute($this->buildSelectQuery(), $this->conditionBuilder->getValues());
        $rows = $rq->fetchAll(PDO::FETCH_NUM);

        foreach ($rows as $row) {
            $hashmap[$row[0]] = $row[1];
        }

        return $hashmap;
    }

    /**
     * Shortcut method to get a hashmap result
     *
     * @access public
     * @param  string  $key    Key column
     * @param  string  $value  Value column
     * @return array
     */
    public function getAll($key, $value)
    {
        $this->keyColumn = $key;
        $this->valueColumn = $value;
        return $this->get();
    }
}
