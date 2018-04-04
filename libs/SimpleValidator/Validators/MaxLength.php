<?php

namespace SimpleValidator\Validators;

class MaxLength extends Base
{
    private $max;

    public function __construct($field, $error_message, $max)
    {
        parent::__construct($field, $error_message);
        $this->max = $max;
    }

    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            $length = mb_strlen($data[$this->field], 'UTF-8');
            return $length <= $this->max;
        }

        return true;
    }
}
