<?php

namespace Kanboard\Core\Plugin;

use PDOException;
use PicoDb\SQLException;
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
     * @param  string $driver
     */
    public static function getSchemaFilename($pluginName, $driver = DB_DRIVER)
    {
        $filename = self::findSchemaFilename($pluginName, $driver);

        if ($filename !== null) {
            return $filename;
        }

        return self::buildSchemaFilename($pluginName, ucfirst(strtolower($driver)));
    }

    /**
     * Get existing schema filename for the given driver
     *
     * @static
     * @access public
     * @param  string $pluginName
     * @param  string $driver
     * @return string|null
     */
    public static function findSchemaFilename($pluginName, $driver = DB_DRIVER)
    {
        foreach (self::getSchemaDriverNames($driver) as $schemaDriver) {
            $filename = self::buildSchemaFilename($pluginName, $schemaDriver);

            if (file_exists($filename)) {
                return $filename;
            }
        }

        return null;
    }

    /**
     * Get candidate schema driver names for a database driver
     *
     * @static
     * @access public
     * @param  string $driver
     * @return string[]
     */
    public static function getSchemaDriverNames($driver = DB_DRIVER)
    {
        $driver = strtolower($driver);
        $schemaDrivers = array(ucfirst($driver));

        if (in_array($driver, array('dblib', 'mssql', 'odbc'), true)) {
            $schemaDrivers[] = 'Mssql';
        }

        return array_values(array_unique($schemaDrivers));
    }

    /**
     * Build schema filename
     *
     * @static
     * @access private
     * @param  string $pluginName
     * @param  string $schemaDriver
     * @return string
     */
    private static function buildSchemaFilename($pluginName, $schemaDriver)
    {
        return PLUGINS_DIR.'/'.$pluginName.'/Schema/'.$schemaDriver.'.php';
    }

    /**
     * Return true if the plugin has schema
     *
     * @static
     * @access public
     * @param  string $pluginName
     * @param  string $driver
     * @return boolean
     */
    public static function hasSchema($pluginName, $driver = DB_DRIVER)
    {
        return self::findSchemaFilename($pluginName, $driver) !== null;
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
        try {
            return (int) $this->db->table(self::TABLE_SCHEMA)->eq('plugin', strtolower($plugin))->findOneColumn('version');
        } catch (SQLException|PDOException $e) {
            throw new RuntimeException('Unable to read plugin schema version for "'.$plugin.'". Run database migrations before loading plugins: ./cli db:migrate => '.$e->getMessage());
        }
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
