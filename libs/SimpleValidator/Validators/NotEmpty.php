<?php

namespace SimpleValidator\Validators;

class NotEmpty extends Base
{
    public function execute(array $data)
    {
        if (array_key_exists($this->field, $data)) {
            return $data[$this->field] !== null && $data[$this->field] !== '';
        }

        return true;
    }
}
