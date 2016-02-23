<?php

namespace SimpleValidator\Validators;

class NotInArray extends InArray
{
    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            return ! in_array($data[$this->field], $this->array);
        }

        return true;
    }
}
