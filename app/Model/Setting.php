<?php

namespace Kanboard\Model;

/**
 * Application Settings
 *
 * @package  model
 * @author   Frederic Guillot
 */
abstract class Setting extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'settings';

    /**
     * Prepare data before save
     *
     * @abstract
     * @access public
     * @return array
     */
    abstract public function prepare(array $values);

    /**
     * Get all settings
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return $this->db->hashtable(self::TABLE)->getAll('option', 'value');
    }

    /**
     * Get a setting value
     *
     * @access public
     * @param  string   $name
     * @param  string   $default
     * @return mixed
     */
    public function getOption($name, $default = '')
    {
        return $this->db
            ->table(self::TABLE)
            ->eq('option', $name)
            ->findOneColumn('value') ?: $default;
    }

    /**
     * Return true if a setting exists
     *
     * @access public
     * @param  string   $name
     * @return boolean
     */
    public function exists($name)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq('option', $name)
            ->exists();
    }

    /**
     * Update or insert new settings
     *
     * @access public
     * @param  array    $values
     */
    public function save(array $values)
    {
        $results = array();
        $values = $this->prepare($values);

        $this->db->startTransaction();

        foreach ($values as $option => $value) {
            if ($this->exists($option)) {
                $results[] = $this->db->table(self::TABLE)->eq('option', $option)->update(array('value' => $value));
            } else {
                $results[] = $this->db->table(self::TABLE)->insert(array('option' => $option, 'value' => $value));
            }
        }

        $this->db->closeTransaction();

        return ! in_array(false, $results, true);
    }
}
