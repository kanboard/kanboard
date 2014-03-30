<?php

namespace PicoDb;

class Mysql extends \PDO {

    private $schema_table = 'schema_version';


    public function __construct(array $settings)
    {
        $required_atttributes = array(
            'hostname',
            'username',
            'password',
            'database',
            'charset',
        );

        foreach ($required_atttributes as $attribute) {
            if (! isset($settings[$attribute])) {
                throw new \LogicException('This configuration parameter is missing: "'.$attribute.'"');
            }
        }

        $dsn = 'mysql:host='.$settings['hostname'].';dbname='.$settings['database'];
        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$settings['charset']
        );

        parent::__construct($dsn, $settings['username'], $settings['password'], $options);

        if (isset($settings['schema_table'])) {
            $this->schema_table = $settings['schema_table'];
        }
    }


    public function getSchemaVersion()
    {
        $this->exec("CREATE TABLE IF NOT EXISTS `".$this->schema_table."` (`version` INT DEFAULT '0')");

        $rq = $this->prepare('SELECT `version` FROM `'.$this->schema_table.'`');
        $rq->execute();
        $result = $rq->fetch(\PDO::FETCH_ASSOC);

        if (isset($result['version'])) {
            return (int) $result['version'];
        }
        else {
            $this->exec('INSERT INTO `'.$this->schema_table.'` VALUES(0)');
        }

        return 0;
    }


    public function setSchemaVersion($version)
    {
        $rq = $this->prepare('UPDATE `'.$this->schema_table.'` SET `version`=?');
        $rq->execute(array($version));
    }


    public function getLastId()
    {
        return $this->lastInsertId();
    }


    public function escapeIdentifier($value)
    {
        if (strpos($value, '.') !== false) return $value;
        return '`'.$value.'`';
    }
}