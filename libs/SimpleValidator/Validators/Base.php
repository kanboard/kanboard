<?php

namespace SimpleValidator\Validators;

abstract class Base
{
    protected $field = '';
    protected $error_message = '';
    protected $data = array();

    abstract public function execute(array $data);

    public function __construct($field, $error_message)
    {
        $this->field = $field;
        $this->error_message = $error_message;
    }

    public function getErrorMessage()
    {
        return $this->error_message;
    }

    public function getField()
    {
        if (is_array($this->field)) {
            return $this->field[0];
        }

        return $this->field;
    }

    public function isFieldNotEmpty(array $data)
    {
        return isset($data[$this->field]) && $data[$this->field] !== '';
    }
}
