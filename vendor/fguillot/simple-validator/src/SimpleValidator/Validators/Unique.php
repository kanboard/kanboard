<?php

namespace SimpleValidator\Validators;

use PDO;

class Unique extends Base
{
    private $pdo;
    private $primary_key;
    private $table;

    public function __construct($field, $error_message, PDO $pdo, $table, $primary_key = 'id')
    {
        parent::__construct($field, $error_message);

        $this->pdo = $pdo;
        $this->primary_key = $primary_key;
        $this->table = $table;
    }

    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            if (! isset($data[$this->primary_key])) {
                $rq = $this->pdo->prepare('SELECT 1 FROM '.$this->table.' WHERE '.$this->field.'=?');
                $rq->execute(array($data[$this->field]));
            }
            else {

                $rq = $this->pdo->prepare(
                    'SELECT 1 FROM '.$this->table.'
                    WHERE '.$this->field.'=? AND '.$this->primary_key.' != ?'
                );

                $rq->execute(array($data[$this->field], $data[$this->primary_key]));
            }

            $result = $rq->fetchColumn();

            if ($result == 1) { // Postgresql returns an integer but other database returns a string '1'
                return false;
            }
        }

        return true;
    }
}
