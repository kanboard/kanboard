<?php

namespace SimpleValidator\Validators;

use SimpleValidator\Base;
use \DateTime;

class Date extends Base
{
    private $formats = array();

    public function __construct($field, $error_message, array $formats)
    {
        parent::__construct($field, $error_message);
        $this->formats = $formats;
    }

    public function execute(array $data)
    {
        if (isset($data[$this->field]) && $data[$this->field] !== '') {

            foreach ($this->formats as $format) {
                if ($this->isValidDate($data[$this->field], $format) === true) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    public function isValidDate($value, $format)
    {
        $date = DateTime::createFromFormat($format, $value);

        if ($date !== false) {
            $errors = DateTime::getLastErrors();
            if ($errors['error_count'] === 0 && $errors['warning_count'] === 0) {
                $timestamp = $date->getTimestamp();
                return $timestamp > 0 ? true : false;
            }
        }

        return false;
    }
}
