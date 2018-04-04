<?php

namespace SimpleValidator\Validators;

class MinLength extends Base
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
            $length = mb_strlen($data[$this->field], 'UTF-8');
            return $length >= $this->min;
        }

        return true;
    }
}
