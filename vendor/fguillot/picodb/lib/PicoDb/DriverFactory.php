<?php

namespace PicoDb;

use LogicException;
use PicoDb\Driver\Mssql;
use PicoDb\Driver\Mysql;
use PicoDb\Driver\Postgres;
use PicoDb\Driver\Sqlite;

/**
 * Class DriverFactory
 *
 * @package PicoDb
 * @author  Frederic Guillot
 */
class DriverFactory
{
    /**
     * Get database driver from settings or environment URL
     *
     * @access public
     * @param  array $settings
     * @return Mssql|Mysql|Postgres|Sqlite
     */
    public static function getDriver(array $settings)
    {
        if (! isset($settings['driver'])) {
            throw new LogicException('You must define a database driver');
        }

        switch ($settings['driver']) {
            case 'sqlite':
                return new Sqlite($settings);
            case 'mssql':
                return new Mssql($settings);
            case 'mysql':
                return new Mysql($settings);
            case 'postgres':
                return new Postgres($settings);
            default:
                throw new LogicException('This database driver is not supported');
        }
    }
}
