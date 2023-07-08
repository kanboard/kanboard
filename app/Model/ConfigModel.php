<?php

namespace Kanboard\Model;

use Kanboard\Core\Security\Token;

/**
 * Config model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ConfigModel extends SettingModel
{
    /**
     * Get a config variable with in-memory caching
     *
     * @access public
     * @param  string   $name            Parameter name
     * @param  string   $default_value   Default value of the parameter
     * @return string
     */
    public function get($name, $default_value = '')
    {
        $options = $this->memoryCache->proxy($this, 'getAll');
        return isset($options[$name]) && $options[$name] !== '' ? $options[$name] : $default_value;
    }

    /**
     * Optimize the Sqlite database
     *
     * @access public
     * @return boolean
     */
    public function optimizeDatabase()
    {
        return $this->db->getConnection()->exec('VACUUM');
    }

    /**
     * Compress the Sqlite database
     *
     * @access public
     * @return string
     */
    public function downloadDatabase()
    {
        return gzencode(file_get_contents(DB_FILENAME));
    }

    /**
     * Replace database file with uploaded one
     *
     * @access public
     * @param  string $file
     * @return bool
     */
    public function uploadDatabase($file)
    {
        $this->db->closeConnection();
        return file_put_contents(DB_FILENAME, gzdecode(file_get_contents($file))) !== false;
    }

    /**
     * Get the Sqlite database size in bytes
     *
     * @access public
     * @return integer
     */
    public function getDatabaseSize()
    {
        return DB_DRIVER === 'sqlite' ? filesize(DB_FILENAME) : 0;
    }

    /**
     * Get database extra options
     *
     * @access public
     * @return array
     */
    public function getDatabaseOptions()
    {
        if (DB_DRIVER === 'sqlite') {
            return [
                'journal_mode' => $this->db->getConnection()->query('PRAGMA journal_mode')->fetchColumn(),
                'wal_autocheckpoint' => $this->db->getConnection()->query('PRAGMA wal_autocheckpoint')->fetchColumn(),
                'synchronous' => $this->db->getConnection()->query('PRAGMA synchronous')->fetchColumn(),
                'busy_timeout' => $this->db->getConnection()->query('PRAGMA busy_timeout')->fetchColumn(),
            ];
        }

        return [];
    }

    /**
     * Regenerate a token
     *
     * @access public
     * @param  string   $option   Parameter name
     * @return boolean
     */
    public function regenerateToken($option)
    {
        return $this->save(array($option => Token::getToken()));
    }

    /**
     * Prepare data before save
     *
     * @access public
     * @param  array $values
     * @return array
     */
    public function prepare(array $values)
    {
        if (! empty($values['application_url']) && substr($values['application_url'], -1) !== '/') {
            $values['application_url'] = $values['application_url'].'/';
        }

        return $values;
    }
}
