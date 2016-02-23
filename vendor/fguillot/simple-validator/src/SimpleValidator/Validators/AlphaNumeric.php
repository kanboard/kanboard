<?php

namespace SimpleValidator\Validators;

class AlphaNumeric extends Base
{
    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            return ctype_alnum($data[$this->field]);
        }

        return true;
    }
}
