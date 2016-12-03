<?php

namespace Kanboard\ServiceProvider;

use LogicException;
use RuntimeException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use PicoDb\Database;

/**
 * Class DatabaseProvider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class DatabaseProvider implements ServiceProviderInterface
{
    /**
     * Register provider
     *
     * @access public
     * @param  Container $container
     * @return Container
     */
    public function register(Container $container)
    {
        $container['db'] = $this->getInstance();

        if (DB_RUN_MIGRATIONS) {
            self::runMigrations($container['db']);
        }

        if (DEBUG) {
            $container['db']->getStatementHandler()
                ->withLogging()
                ->withStopWatch()
            ;
        }

        return $container;
    }

    /**
     * Setup the database driver
     *
     * @access public
     * @return \PicoDb\Database
     */
    public function getInstance()
    {
        switch (DB_DRIVER) {
            case 'sqlite':
                $db = $this->getSqliteInstance();
                break;
            case 'mysql':
                $db = $this->getMysqlInstance();
                break;
            case 'postgres':
                $db = $this->getPostgresInstance();
                break;
            default:
                throw new LogicException('Database driver not supported');
        }

        return $db;
    }

    /**
     * Get current database version
     *
     * @static
     * @access public
     * @param  Database $db
     * @return int
     */
    public static function getSchemaVersion(Database $db)
    {
        return $db->getDriver()->getSchemaVersion();
    }

    /**
     * Execute database migrations
     *
     * @static
     * @access public
     * @throws RuntimeException
     * @param  Database $db
     * @return bool
     */
    public static function runMigrations(Database $db)
    {
        if (! $db->schema()->check(\Schema\VERSION)) {
            $messages = $db->getLogMessages();
            throw new RuntimeException('Unable to run SQL migrations: '.implode(', ', $messages).' (You may have to fix it manually)');
        }

        return true;
    }

    /**
     * Setup the Sqlite database driver
     *
     * @access private
     * @return \PicoDb\Database
     */
    private function getSqliteInstance()
    {
        require_once __DIR__.'/../Schema/Sqlite.php';

        return new Database(array(
            'driver' => 'sqlite',
            'filename' => DB_FILENAME,
        ));
    }

    /**
     * Setup the Mysql database driver
     *
     * @access private
     * @return \PicoDb\Database
     */
    private function getMysqlInstance()
    {
        require_once __DIR__.'/../Schema/Mysql.php';

        return new Database(array(
            'driver'   => 'mysql',
            'hostname' => DB_HOSTNAME,
            'username' => DB_USERNAME,
            'password' => DB_PASSWORD,
            'database' => DB_NAME,
            'charset'  => 'utf8',
            'port'     => DB_PORT,
            'ssl_key'  => DB_SSL_KEY,
            'ssl_ca'   => DB_SSL_CA,
            'ssl_cert' => DB_SSL_CERT,
        ));
    }

    /**
     * Setup the Postgres database driver
     *
     * @access private
     * @return \PicoDb\Database
     */
    private function getPostgresInstance()
    {
        require_once __DIR__.'/../Schema/Postgres.php';

        return new Database(array(
            'driver'   => 'postgres',
            'hostname' => DB_HOSTNAME,
            'username' => DB_USERNAME,
            'password' => DB_PASSWORD,
            'database' => DB_NAME,
            'port'     => DB_PORT,
        ));
    }
}
