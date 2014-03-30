<?php

namespace PicoDb;

class Sqlite extends \PDO {


    public function __construct(array $settings)
    {
        $required_atttributes = array(
            'filename',
        );

        foreach ($required_atttributes as $attribute) {
            if (! isset($settings[$attribute])) {
                throw new \LogicException('This configuration parameter is missing: "'.$attribute.'"');
            }
        }

        parent::__construct('sqlite:'.$settings['filename']);

        $this->exec('PRAGMA foreign_keys = ON');
    }


    public function getSchemaVersion()
    {
        $rq = $this->prepare('PRAGMA user_version');
        $rq->execute();
        $result = $rq->fetch(\PDO::FETCH_ASSOC);

        if (isset($result['user_version'])) {
            return (int) $result['user_version'];
        }

        return 0;
    }


    public function setSchemaVersion($version)
    {
        $this->exec('PRAGMA user_version='.$version);
    }


    public function getLastId()
    {
        return $this->lastInsertId();
    }


    public function escapeIdentifier($value)
    {
        if (strpos($value, '.') !== false) return $value;
        return '"'.$value.'"';
    }
}