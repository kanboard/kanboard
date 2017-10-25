<?php

namespace PicoDb;

use PDO;
use PicoDb\Builder\InsertBuilder;
use PicoDb\Builder\UpdateBuilder;

/**
 * Handle Large Objects (LOBs)
 *
 * @package PicoDb
 * @author  Frederic Guillot
 */
class LargeObject extends Table
{
    /**
     * Fetch large object as file descriptor
     *
     * This method is not compatible with Sqlite and Mysql (return a string instead of resource)
     *
     * @access public
     * @param  string $column
     * @return resource
     */
    public function findOneColumnAsStream($column)
    {
        $this->limit(1);
        $this->columns($column);

        $rq = $this->db->getStatementHandler()
            ->withSql($this->buildSelectQuery())
            ->withPositionalParams($this->conditionBuilder->getValues())
            ->execute();

        $rq->bindColumn($column, $fd, PDO::PARAM_LOB);
        $rq->fetch(PDO::FETCH_BOUND);

        return $fd;
    }

    /**
     * Fetch large object as string
     *
     * @access public
     * @param  string $column
     * @return string
     */
    public function findOneColumnAsString($column)
    {
        $fd = $this->findOneColumnAsStream($column);

        if (is_string($fd)) {
            return $fd;
        }

        return stream_get_contents($fd);
    }

    /**
     * Insert large object from stream
     *
     * @access public
     * @param  string           $blobColumn
     * @param  resource|string  $blobDescriptor
     * @param  array            $data
     * @return bool
     */
    public function insertFromStream($blobColumn, &$blobDescriptor, array $data = array())
    {
        $columns = array_merge(array($blobColumn), array_keys($data));
        $this->db->startTransaction();

        $result =  $this->db->getStatementHandler()
            ->withSql(InsertBuilder::getInstance($this->db, $this->conditionBuilder)
                ->withTable($this->name)
                ->withColumns($columns)
                ->build()
            )
            ->withNamedParams($data)
            ->withLobParam($blobColumn, $blobDescriptor)
            ->execute();

        $this->db->closeTransaction();

        return $result !== false;
    }

    /**
     * Insert large object from file
     *
     * @access public
     * @param  string $blobColumn
     * @param  string $filename
     * @param  array $data
     * @return bool
     */
    public function insertFromFile($blobColumn, $filename, array $data = array())
    {
        $fp = fopen($filename, 'rb');
        $result = $this->insertFromStream($blobColumn, $fp, $data);
        fclose($fp);
        return $result;
    }

    /**
     * Insert large object from string
     *
     * @access public
     * @param  string $blobColumn
     * @param  string $blobData
     * @param  array $data
     * @return bool
     */
    public function insertFromString($blobColumn, &$blobData, array $data = array())
    {
        return $this->insertFromStream($blobColumn, $blobData, $data);
    }

    /**
     * Update large object from stream
     *
     * @access public
     * @param  string   $blobColumn
     * @param  resource $blobDescriptor
     * @param  array    $data
     * @return bool
     */
    public function updateFromStream($blobColumn, &$blobDescriptor, array $data = array())
    {
        $values = array_merge(array_values($data), $this->conditionBuilder->getValues());
        $columns = array_merge(array($blobColumn), array_keys($data));

        $this->db->startTransaction();

        $result =  $this->db->getStatementHandler()
            ->withSql(UpdateBuilder::getInstance($this->db, $this->conditionBuilder)
                ->withTable($this->name)
                ->withColumns($columns)
                ->build()
            )
            ->withPositionalParams($values)
            ->withLobParam($blobColumn, $blobDescriptor)
            ->execute();

        $this->db->closeTransaction();

        return $result !== false;
    }

    /**
     * Update large object from file
     *
     * @access public
     * @param  string $blobColumn
     * @param  string $filename
     * @param  array $data
     * @return bool
     */
    public function updateFromFile($blobColumn, $filename, array $data = array())
    {
        $fp = fopen($filename, 'r');
        $result = $this->updateFromStream($blobColumn, $fp, $data);
        fclose($fp);
        return $result;
    }
}
