<?php

namespace SimpleValidator\Validators;

class Length extends Base
{
    private $min;
    private $max;

    public function __construct($field, $error_message, $min, $max)
    {
        parent::__construct($field, $error_message);
        $this->min = $min;
        $this->max = $max;
    }

    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            $length = mb_strlen($data[$this->field], 'UTF-8');
            return $length >= $this->min && $length <= $this->max;
        }

        return true;
    }
}
