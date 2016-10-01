<?php

namespace Kanboard\Core\Plugin;

use PDOException;
use RuntimeException;

/**
 * Class SchemaHandler
 *
 * @package Kanboard\Core\Plugin
 * @author  Frederic Guillot
 */
class SchemaHandler extends \Kanboard\Core\Base
{
    /**
     * Schema version table for plugins
     *
     * @var string
     */
    const TABLE_SCHEMA = 'plugin_schema_versions';

    /**
     * Get schema filename
     *
     * @static
     * @access public
     * @param  string $pluginName
     * @return string
     */
    public static function getSchemaFilename($pluginName)
    {
        return PLUGINS_DIR.'/'.$pluginName.'/Schema/'.ucfirst(DB_DRIVER).'.php';
    }

    /**
     * Return true if the plugin has schema
     *
     * @static
     * @access public
     * @param  string $pluginName
     * @return boolean
     */
    public static function hasSchema($pluginName)
    {
        return file_exists(self::getSchemaFilename($pluginName));
    }

    /**
     * Load plugin schema
     *
     * @access public
     * @param  string $pluginName
     */
    public function loadSchema($pluginName)
    {
        require_once self::getSchemaFilename($pluginName);
        $this->migrateSchema($pluginName);
    }

    /**
     * Execute plugin schema migrations
     *
     * @access public
     * @param  string $pluginName
     */
    public function migrateSchema($pluginName)
    {
        $lastVersion = constant('\Kanboard\Plugin\\'.$pluginName.'\Schema\VERSION');
        $currentVersion = $this->getSchemaVersion($pluginName);

        try {
            $this->db->startTransaction();
            $this->db->getDriver()->disableForeignKeys();

            for ($i = $currentVersion + 1; $i <= $lastVersion; $i++) {
                $functionName = '\Kanboard\Plugin\\'.$pluginName.'\Schema\version_'.$i;

                if (function_exists($functionName)) {
                    call_user_func($functionName, $this->db->getConnection());
                }
            }

            $this->db->getDriver()->enableForeignKeys();
            $this->db->closeTransaction();
            $this->setSchemaVersion($pluginName, $i - 1);
        } catch (PDOException $e) {
            $this->db->cancelTransaction();
            $this->db->getDriver()->enableForeignKeys();
            throw new RuntimeException('Unable to migrate schema for the plugin: '.$pluginName.' => '.$e->getMessage());
        }
    }

    /**
     * Get current plugin schema version
     *
     * @access public
     * @param  string  $plugin
     * @return integer
     */
    public function getSchemaVersion($plugin)
    {
        return (int) $this->db->table(self::TABLE_SCHEMA)->eq('plugin', strtolower($plugin))->findOneColumn('version');
    }

    /**
     * Save last plugin schema version
     *
     * @access public
     * @param  string   $plugin
     * @param  integer  $version
     * @return boolean
     */
    public function setSchemaVersion($plugin, $version)
    {
        $dictionary = array(
            strtolower($plugin) => $version
        );

        return $this->db->getDriver()->upsert(self::TABLE_SCHEMA, 'plugin', 'version', $dictionary);
    }
}
