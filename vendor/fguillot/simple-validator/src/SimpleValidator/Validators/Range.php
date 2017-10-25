<?php

namespace SimpleValidator\Validators;

class Range extends Base
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

            if (! is_numeric($data[$this->field])) {
                return false;
            }

            if ($data[$this->field] < $this->min || $data[$this->field] > $this->max) {
                return false;
            }
        }

        return true;
    }
}
