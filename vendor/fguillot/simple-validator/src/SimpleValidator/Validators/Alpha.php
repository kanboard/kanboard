<?php

namespace SimpleValidator\Validators;

class Alpha extends Base
{
    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            return ctype_alpha($data[$this->field]);
        }

        return true;
    }
}
