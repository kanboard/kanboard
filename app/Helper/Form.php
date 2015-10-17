<?php

namespace Kanboard\Helper;

use Kanboard\Core\Security;

/**
 * Form helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class Form extends \Kanboard\Core\Base
{
    /**
     * Hidden CSRF token field
     *
     * @access public
     * @return string
     */
    public function csrf()
    {
        return '<input type="hidden" name="csrf_token" value="'.Security::getCSRFToken().'"/>';
    }

    /**
     * Display a hidden form field
     *
     * @access public
     * @param  string  $name    Field name
     * @param  array   $values  Form values
     * @return string
     */
    public function hidden($name, array $values = array())
    {
        return '<input type="hidden" name="'.$name.'" id="form-'.$name.'" '.$this->formValue($values, $name).'/>';
    }

    /**
     * Display a select field
     *
     * @access public
     * @param  string  $name     Field name
     * @param  array   $options  Options
     * @param  array   $values   Form values
     * @param  array   $errors   Form errors
     * @param  string  $class    CSS class
     * @return string
     */
    public function select($name, array $options, array $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        $html = '<select name="'.$name.'" id="form-'.$name.'" class="'.$class.'" '.implode(' ', $attributes).'>';

        foreach ($options as $id => $value) {
            $html .= '<option value="'.$this->helper->e($id).'"';

            if (isset($values->$name) && $id == $values->$name) {
                $html .= ' selected="selected"';
            }
            if (isset($values[$name]) && $id == $values[$name]) {
                $html .= ' selected="selected"';
            }

            $html .= '>'.$this->helper->e($value).'</option>';
        }

        $html .= '</select>';
        $html .= $this->errorList($errors, $name);

        return $html;
    }

    /**
     * Display a radio field group
     *
     * @access public
     * @param  string  $name     Field name
     * @param  array   $options  Options
     * @param  array   $values   Form values
     * @return string
     */
    public function radios($name, array $options, array $values = array())
    {
        $html = '';

        foreach ($options as $value => $label) {
            $html .= $this->radio($name, $label, $value, isset($values[$name]) && $values[$name] == $value);
        }

        return $html;
    }

    /**
     * Display a radio field
     *
     * @access public
     * @param  string  $name      Field name
     * @param  string  $label     Form label
     * @param  string  $value     Form value
     * @param  boolean $selected  Field selected or not
     * @param  string  $class     CSS class
     * @return string
     */
    public function radio($name, $label, $value, $selected = false, $class = '')
    {
        return '<label><input type="radio" name="'.$name.'" class="'.$class.'" value="'.$this->helper->e($value).'" '.($selected ? 'checked="checked"' : '').'> '.$this->helper->e($label).'</label>';
    }

    /**
     * Display a checkboxes group
     *
     * @access public
     * @param  string  $name     Field name
     * @param  array   $options  Options
     * @param  array   $values   Form values
     * @return string
     */
    public function checkboxes($name, array $options, array $values = array())
    {
        $html = '';

        foreach ($options as $value => $label) {
            $html .= $this->checkbox($name.'['.$value.']', $label, $value, isset($values[$name]) && in_array($value, $values[$name]));
        }

        return $html;
    }

    /**
     * Display a checkbox field
     *
     * @access public
     * @param  string  $name      Field name
     * @param  string  $label     Form label
     * @param  string  $value     Form value
     * @param  boolean $checked   Field selected or not
     * @param  string  $class     CSS class
     * @return string
     */
    public function checkbox($name, $label, $value, $checked = false, $class = '')
    {
        return '<label><input type="checkbox" name="'.$name.'" class="'.$class.'" value="'.$this->helper->e($value).'" '.($checked ? 'checked="checked"' : '').'>&nbsp;'.$this->helper->e($label).'</label>';
    }

    /**
     * Display a form label
     *
     * @access public
     * @param  string  $name        Field name
     * @param  string  $label       Form label
     * @param  array   $attributes  HTML attributes
     * @return string
     */
    public function label($label, $name, array $attributes = array())
    {
        return '<label for="form-'.$name.'" '.implode(' ', $attributes).'>'.$this->helper->e($label).'</label>';
    }

    /**
     * Display a textarea
     *
     * @access public
     * @param  string  $name        Field name
     * @param  array   $values      Form values
     * @param  array   $errors      Form errors
     * @param  array   $attributes  HTML attributes
     * @param  string  $class       CSS class
     * @return string
     */
    public function textarea($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        $class .= $this->errorClass($errors, $name);

        $html = '<textarea name="'.$name.'" id="form-'.$name.'" class="'.$class.'" ';
        $html .= implode(' ', $attributes).'>';
        $html .= isset($values->$name) ? $this->helper->e($values->$name) : isset($values[$name]) ? $values[$name] : '';
        $html .= '</textarea>';
        $html .= $this->errorList($errors, $name);

        return $html;
    }

    /**
     * Display file field
     *
     * @access public
     * @param  string  $name
     * @param  array   $errors
     * @param  boolean $multiple
     * @return string
     */
    public function file($name, array $errors = array(), $multiple = false)
    {
        $html = '<input type="file" name="'.$name.'" id="form-'.$name.'" '.($multiple ? 'multiple' : '').'>';
        $html .= $this->errorList($errors, $name);

        return $html;
    }

    /**
     * Display a input field
     *
     * @access public
     * @param  string  $type        HMTL input tag type
     * @param  string  $name        Field name
     * @param  array   $values      Form values
     * @param  array   $errors      Form errors
     * @param  array   $attributes  HTML attributes
     * @param  string  $class       CSS class
     * @return string
     */
    public function input($type, $name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        $class .= $this->errorClass($errors, $name);

        $html = '<input type="'.$type.'" name="'.$name.'" id="form-'.$name.'" '.$this->formValue($values, $name).' class="'.$class.'" ';
        $html .= implode(' ', $attributes).'>';

        if (in_array('required', $attributes)) {
            $html .= '<span class="form-required">*</span>';
        }

        $html .= $this->errorList($errors, $name);

        return $html;
    }

    /**
     * Display a text field
     *
     * @access public
     * @param  string  $name        Field name
     * @param  array   $values      Form values
     * @param  array   $errors      Form errors
     * @param  array   $attributes  HTML attributes
     * @param  string  $class       CSS class
     * @return string
     */
    public function text($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        return $this->input('text', $name, $values, $errors, $attributes, $class);
    }

    /**
     * Display a password field
     *
     * @access public
     * @param  string  $name        Field name
     * @param  array   $values      Form values
     * @param  array   $errors      Form errors
     * @param  array   $attributes  HTML attributes
     * @param  string  $class       CSS class
     * @return string
     */
    public function password($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        return $this->input('password', $name, $values, $errors, $attributes, $class);
    }

    /**
     * Display an email field
     *
     * @access public
     * @param  string  $name        Field name
     * @param  array   $values      Form values
     * @param  array   $errors      Form errors
     * @param  array   $attributes  HTML attributes
     * @param  string  $class       CSS class
     * @return string
     */
    public function email($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        return $this->input('email', $name, $values, $errors, $attributes, $class);
    }

    /**
     * Display a number field
     *
     * @access public
     * @param  string  $name        Field name
     * @param  array   $values      Form values
     * @param  array   $errors      Form errors
     * @param  array   $attributes  HTML attributes
     * @param  string  $class       CSS class
     * @return string
     */
    public function number($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        return $this->input('number', $name, $values, $errors, $attributes, $class);
    }

    /**
     * Display a numeric field (allow decimal number)
     *
     * @access public
     * @param  string  $name        Field name
     * @param  array   $values      Form values
     * @param  array   $errors      Form errors
     * @param  array   $attributes  HTML attributes
     * @param  string  $class       CSS class
     * @return string
     */
    public function numeric($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        return $this->input('text', $name, $values, $errors, $attributes, $class.' form-numeric');
    }

    /**
     * Display the form error class
     *
     * @access private
     * @param array   $errors   Error list
     * @param string  $name     Field name
     * @return string
     */
    private function errorClass(array $errors, $name)
    {
        return ! isset($errors[$name]) ? '' : ' form-error';
    }

    /**
     * Display a list of form errors
     *
     * @access private
     * @param array   $errors   List of errors
     * @param string  $name     Field name
     * @return string
     */
    private function errorList(array $errors, $name)
    {
        $html = '';

        if (isset($errors[$name])) {
            $html .= '<ul class="form-errors">';

            foreach ($errors[$name] as $error) {
                $html .= '<li>'.$this->helper->e($error).'</li>';
            }

            $html .= '</ul>';
        }

        return $html;
    }

    /**
     * Get an escaped form value
     *
     * @access private
     * @param  mixed  $values  Values
     * @param  string $name    Field name
     * @return string
     */
    private function formValue($values, $name)
    {
        if (isset($values->$name)) {
            return 'value="'.$this->helper->e($values->$name).'"';
        }

        return isset($values[$name]) ? 'value="'.$this->helper->e($values[$name]).'"' : '';
    }
}
