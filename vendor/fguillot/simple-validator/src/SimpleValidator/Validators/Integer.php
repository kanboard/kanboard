<?php

namespace SimpleValidator\Validators;

class Integer extends Base
{
    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            if (is_string($data[$this->field])) {

                if ($data[$this->field][0] === '-') {
                    return ctype_digit(substr($data[$this->field], 1));
                }

                return ctype_digit($data[$this->field]);
            }
            else {
                return is_int($data[$this->field]);
            }
        }

        return true;
    }
}
