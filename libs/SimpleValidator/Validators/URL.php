<?php

namespace SimpleValidator\Validators;

class URL extends Base
{
    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            return filter_var($data[$this->field], FILTER_VALIDATE_URL) !== false;
        }

        return true;
    }
}