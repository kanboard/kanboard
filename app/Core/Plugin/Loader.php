<?php

namespace Kanboard\Core\Plugin;

use DirectoryIterator;
use PDOException;
use RuntimeException;
use Kanboard\Core\Tool;

/**
 * Plugin Loader
 *
 * @package  plugin
 * @author   Frederic Guillot
 */
class Loader extends \Kanboard\Core\Base
{
    /**
     * Schema version table for plugins
     *
     * @var string
     */
    const TABLE_SCHEMA = 'plugin_schema_versions';

    /**
     * Plugin instances
     *
     * @access public
     * @var array
     */
    public $plugins = array();

    /**
     * Scan plugin folder and load plugins
     *
     * @access public
     */
    public function scan()
    {
        if (file_exists(PLUGINS_DIR)) {
            $dir = new DirectoryIterator(PLUGINS_DIR);

            foreach ($dir as $fileinfo) {
                if (! $fileinfo->isDot() && $fileinfo->isDir()) {
                    $plugin = $fileinfo->getFilename();
                    $this->loadSchema($plugin);
                    $this->load($plugin);
                }
            }
        }
    }

    /**
     * Load plugin
     *
     * @access public
     * @param  string $plugin
     */
    public function load($plugin)
    {
        $class = '\Kanboard\Plugin\\'.$plugin.'\\Plugin';
        $instance = new $class($this->container);

        Tool::buildDic($this->container, $instance->getClasses());

        $instance->initialize();
        $this->plugins[] = $instance;
    }

    /**
     * Load plugin schema
     *
     * @access public
     * @param  string  $plugin
     */
    public function loadSchema($plugin)
    {
        $filename = PLUGINS_DIR.'/'.$plugin.'/Schema/'.ucfirst(DB_DRIVER).'.php';

        if (file_exists($filename)) {
            require_once($filename);
            $this->migrateSchema($plugin);
        }
    }

    /**
     * Execute plugin schema migrations
     *
     * @access public
     * @param  string  $plugin
     */
    public function migrateSchema($plugin)
    {
        $last_version = constant('\Kanboard\Plugin\\'.$plugin.'\Schema\VERSION');
        $current_version = $this->getSchemaVersion($plugin);

        try {
            $this->db->startTransaction();
            $this->db->getDriver()->disableForeignKeys();

            for ($i = $current_version + 1; $i <= $last_version; $i++) {
                $function_name = '\Kanboard\Plugin\\'.$plugin.'\Schema\version_'.$i;

                if (function_exists($function_name)) {
                    call_user_func($function_name, $this->db->getConnection());
                }
            }

            $this->db->getDriver()->enableForeignKeys();
            $this->db->closeTransaction();
            $this->setSchemaVersion($plugin, $i - 1);
        } catch (PDOException $e) {
            $this->db->cancelTransaction();
            $this->db->getDriver()->enableForeignKeys();
            throw new RuntimeException('Unable to migrate schema for the plugin: '.$plugin.' => '.$e->getMessage());
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
