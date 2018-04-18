<?php

namespace SimpleValidator\Validators;

class GreaterThan extends Base
{
    private $min;

    public function __construct($field, $error_message, $min)
    {
        parent::__construct($field, $error_message);
        $this->min = $min;
    }

    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            return $data[$this->field] > $this->min;
        }

        return true;
    }
}
