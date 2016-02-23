<?php

namespace SimpleValidator\Validators;

use PDO;

class Exists extends Base
{
    private $pdo;
    private $key;
    private $table;

    public function __construct($field, $error_message, PDO $pdo, $table, $key = '')
    {
        parent::__construct($field, $error_message);

        $this->pdo = $pdo;
        $this->table = $table;
        $this->key = $key;
    }


    public function execute(array $data)
    {
        if (! $this->isFieldNotEmpty($data)) {
            return true;
        }

        if ($this->key === '') {
            $this->key = $this->field;
        }

        $rq = $this->pdo->prepare('SELECT 1 FROM '.$this->table.' WHERE '.$this->key.'=?');
        $rq->execute(array($data[$this->field]));

        return $rq->fetchColumn() == 1;
    }
}
