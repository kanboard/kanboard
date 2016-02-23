<?php

namespace SimpleValidator\Validators;

class Ip extends Base
{
    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            return filter_var($data[$this->field], FILTER_VALIDATE_IP) !== false;
        }

        return true;
    }
}
