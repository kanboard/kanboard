<?php

namespace SimpleValidator\Validators;

class Required extends Base
{
    public function execute(array $data)
    {
        return $this->isFieldNotEmpty($data);
    }
}
