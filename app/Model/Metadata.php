<?php

namespace Kanboard\Model;

/**
 * Metadata
 *
 * @package  model
 * @author   Frederic Guillot
 */
abstract class Metadata extends Base
{
    /**
     * Define the entity key
     *
     * @abstract
     * @access protected
     * @return string
     */
    abstract protected function getEntityKey();

    /**
     * Get all metadata for the entity
     *
     * @access public
     * @param  integer  $entity_id
     * @return array
     */
    public function getAll($entity_id)
    {
        return $this->db
            ->hashtable(static::TABLE)
            ->eq($this->getEntityKey(), $entity_id)
            ->asc('name')
            ->getAll('name', 'value');
    }

    /**
     * Get a metadata for the given entity
     *
     * @access public
     * @param  integer  $entity_id
     * @param  string   $name
     * @param  string   $default
     * @return mixed
     */
    public function get($entity_id, $name, $default = '')
    {
        return $this->db
            ->table(static::TABLE)
            ->eq($this->getEntityKey(), $entity_id)
            ->eq('name', $name)
            ->findOneColumn('value') ?: $default;
    }

    /**
     * Return true if a metadata exists
     *
     * @access public
     * @param  integer  $entity_id
     * @param  string   $name
     * @return boolean
     */
    public function exists($entity_id, $name)
    {
        return $this->db
            ->table(static::TABLE)
            ->eq($this->getEntityKey(), $entity_id)
            ->eq('name', $name)
            ->exists();
    }

    /**
     * Update or insert new metadata
     *
     * @access public
     * @param  integer  $entity_id
     * @param  array    $values
     */
    public function save($entity_id, array $values)
    {
        $results = array();

        $this->db->startTransaction();

        foreach ($values as $key => $value) {
            if ($this->exists($entity_id, $key)) {
                $results[] = $this->db->table(static::TABLE)->eq($this->getEntityKey(), $entity_id)->eq('name', $key)->update(array('value' => $value));
            } else {
                $results[] = $this->db->table(static::TABLE)->insert(array('name' => $key, 'value' => $value, $this->getEntityKey() => $entity_id));
            }
        }

        $this->db->closeTransaction();

        return ! in_array(false, $results, true);
    }
}
