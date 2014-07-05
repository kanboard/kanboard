<?php

namespace PicoDb;

class Postgres extends \PDO {

    private $schema_table = 'schema_version';


    public function __construct(array $settings)
    {
        $required_atttributes = array(
            'hostname',
            'username',
            'password',
            'database',
        );

        foreach ($required_atttributes as $attribute) {
            if (! isset($settings[$attribute])) {
                throw new \LogicException('This configuration parameter is missing: "'.$attribute.'"');
            }
        }

        $dsn = 'pgsql:host='.$settings['hostname'].';dbname='.$settings['database'];

        parent::__construct($dsn, $settings['username'], $settings['password']);

        if (isset($settings['schema_table'])) {
            $this->schema_table = $settings['schema_table'];
        }
    }


    public function getSchemaVersion()
    {
        $this->exec("CREATE TABLE IF NOT EXISTS ".$this->schema_table." (version SMALLINT DEFAULT 0)");

        $rq = $this->prepare('SELECT version FROM '.$this->schema_table.'');
        $rq->execute();
        $result = $rq->fetch(\PDO::FETCH_ASSOC);

        if (isset($result['version'])) {
            return (int) $result['version'];
        }
        else {
            $this->exec('INSERT INTO '.$this->schema_table.' VALUES(0)');
        }

        return 0;
    }


    public function setSchemaVersion($version)
    {
        $rq = $this->prepare('UPDATE '.$this->schema_table.' SET version=?');
        $rq->execute(array($version));
    }


    public function getLastId()
    {
        $rq = $this->prepare('SELECT LASTVAL()');
        $rq->execute();
        return $rq->fetchColumn();
    }


    public function escapeIdentifier($value)
    {
        return $value;
    }
}