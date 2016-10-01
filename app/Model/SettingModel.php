<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Application Settings
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
abstract class SettingModel extends Base
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
     * @param  array $values
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
        $value = $this->db
            ->table(self::TABLE)
            ->eq('option', $name)
            ->findOneColumn('value');

        return $value === null || $value === false || $value === '' ? $default : $value;
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
     * @return boolean
     */
    public function save(array $values)
    {
        $results = array();
        $values = $this->prepare($values);
        $user_id = $this->userSession->getId();
        $timestamp = time();

        $this->db->startTransaction();

        foreach ($values as $option => $value) {
            if ($this->exists($option)) {
                $results[] = $this->db->table(self::TABLE)->eq('option', $option)->update(array(
                    'value' => $value,
                    'changed_on' => $timestamp,
                    'changed_by' => $user_id,
                ));
            } else {
                $results[] = $this->db->table(self::TABLE)->insert(array(
                    'option' => $option,
                    'value' => $value,
                    'changed_on' => $timestamp,
                    'changed_by' => $user_id,
                ));
            }
        }

        $this->db->closeTransaction();

        return ! in_array(false, $results, true);
    }
}
