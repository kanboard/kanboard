<?php

namespace SimpleValidator\Validators;

class InArray extends Base
{
    protected $array;

    public function __construct($field, array $array, $error_message)
    {
        parent::__construct($field, $error_message);
        $this->array = $array;
    }

    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            return in_array($data[$this->field], $this->array);
        }

        return true;
    }
}
