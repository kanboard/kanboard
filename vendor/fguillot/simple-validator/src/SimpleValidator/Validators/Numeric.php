<?php

namespace SimpleValidator\Validators;

class Numeric extends Base
{
    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            return is_numeric($data[$this->field]);
        }

        return true;
    }
}
