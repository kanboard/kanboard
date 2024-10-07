<?php

namespace SimpleValidator\Validators;

class Timezone extends Base
{
    public function execute(array $data)
    {
        if ($this->isFieldNotEmpty($data)) {
            return in_array($data[$this->field], timezone_identifiers_list());
        }

        return true;
    }
}
