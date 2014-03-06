<?php

namespace SimpleValidator\Validators;

use SimpleValidator\Base;

class Date extends Base
{
    private $format;

    public function __construct($field, $error_message, $format)
    {
        parent::__construct($field, $error_message);
        $this->format = $format;
    }

    public function execute(array $data)
    {
        if (isset($data[$this->field]) && $data[$this->field] !== '') {

            $date = \DateTime::createFromFormat($this->format, $data[$this->field]);

            if ($date !== false) {
                $errors = \DateTime::getLastErrors();
                return $errors['error_count'] === 0 && $errors['warning_count'] === 0;
            }

            return false;
        }

        return true;
    }
}
