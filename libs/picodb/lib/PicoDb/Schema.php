<?php

namespace PicoDb;

use PDOException;

/**
 * Schema migration class
 *
 * @package PicoDb
 * @author  Frederic Guillot
 */
class Schema
{
    /**
     * Database instance
     *
     * @access protected
     * @var Database
     */
    protected $db = null;

    /**
     * Schema namespace
     *
     * @access protected
     * @var string
     */
    protected $namespace = '\Schema';

    /**
     * Constructor
     *
     * @access public
     * @param  Database  $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Set another namespace
     *
     * @access public
     * @param  string $namespace
     * @return Schema
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Get schema namespace
     *
     * @access public
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Check the schema version and run the migrations
     *
     * @access public
     * @param  integer  $last_version
     * @return boolean
     */
    public function check($last_version = 1)
    {
        $current_version = $this->db->getDriver()->getSchemaVersion();

        if ($current_version < $last_version) {
            return $this->migrateTo($current_version, $last_version);
        }

        return true;
    }

    /**
     * Migrate the schema to one version to another
     *
     * @access public
     * @param  integer  $current_version
     * @param  integer  $next_version
     * @return boolean
     */
    public function migrateTo($current_version, $next_version)
    {
        try {
            for ($i = $current_version + 1; $i <= $next_version; $i++) {
                $this->db->getDriver()->disableForeignKeys();
                $this->db->startTransaction();

                $function_name = $this->getNamespace().'\version_'.$i;

                if (function_exists($function_name)) {
                    $this->db->setLogMessage('Running migration '.$function_name);
                    call_user_func($function_name, $this->db->getConnection());
                }

                $this->db->getDriver()->setSchemaVersion($i);
                $this->db->closeTransaction();
                $this->db->getDriver()->enableForeignKeys();
            }
        } catch (PDOException $e) {
            $this->db->setLogMessage($e->getMessage());
            $this->db->cancelTransaction();
            $this->db->getDriver()->enableForeignKeys();
            return false;
        }

        return true;
    }
}
