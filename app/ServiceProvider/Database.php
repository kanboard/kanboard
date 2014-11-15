<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use PicoDb\Database as Dbal;

class Database implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['db'] = $this->getInstance();
    }

    /**
     * Setup the database driver and execute schema migration
     *
     * @return PicoDb\Database
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
                die('Database driver not supported');
        }

        if ($db->schema()->check(\Schema\VERSION)) {
            return $db;
        }
        else {
            $errors = $db->getLogMessages();
            die('Unable to migrate database schema: <br/><br/><strong>'.(isset($errors[0]) ? $errors[0] : 'Unknown error').'</strong>');
        }
    }

    /**
     * Setup the Sqlite database driver
     *
     * @return PicoDb\Database
     */
    function getSqliteInstance()
    {
        require_once __DIR__.'/../Schema/Sqlite.php';

        return new Dbal(array(
            'driver' => 'sqlite',
            'filename' => DB_FILENAME
        ));
    }

    /**
     * Setup the Mysql database driver
     *
     * @return PicoDb\Database
     */
    function getMysqlInstance()
    {
        require_once __DIR__.'/../Schema/Mysql.php';

        return new Dbal(array(
            'driver'   => 'mysql',
            'hostname' => DB_HOSTNAME,
            'username' => DB_USERNAME,
            'password' => DB_PASSWORD,
            'database' => DB_NAME,
            'charset'  => 'utf8',
        ));
    }

    /**
     * Setup the Postgres database driver
     *
     * @return PicoDb\Database
     */
    public function getPostgresInstance()
    {
        require_once __DIR__.'/../Schema/Postgres.php';

        return new Dbal(array(
            'driver'   => 'postgres',
            'hostname' => DB_HOSTNAME,
            'username' => DB_USERNAME,
            'password' => DB_PASSWORD,
            'database' => DB_NAME,
        ));
    }
}
