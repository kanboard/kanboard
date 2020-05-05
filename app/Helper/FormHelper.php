<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Form helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class FormHelper extends Base
{
    /**
     * Hidden CSRF token field
     *
     * @access public
     * @return string
     */
    public function csrf()
    {
        return '<input type="hidden" name="csrf_token" value="'.$this->token->getCSRFToken().'"/>';
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
     * @param  string $name Field name
     * @param  array $options Options
     * @param  array $values Form values
     * @param  array $errors Form errors
     * @param  array $attributes
     * @param  string $class CSS class
     * @return string
     */
    public function select($name, array $options, array $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        $html = '<select name="'.$name.'" id="form-'.$name.'" class="'.$class.'" '.implode(' ', $attributes).'>';

        foreach ($options as $id => $value) {
            $html .= '<option value="'.$this->helper->text->e($id).'"';

            if (isset($values->$name) && $id == $values->$name) {
                $html .= ' selected="selected"';
            }
            if (isset($values[$name]) && $id == $values[$name]) {
                $html .= ' selected="selected"';
            }

            $html .= '>'.$this->helper->text->e($value).'</option>';
        }

        $html .= '</select>';
        $html .= $this->errorList($errors, $name);

        return $html;
    }

    /**
     * Display a color select field
     *
     * @access public
     * @param  string $name Field name
     * @param  array $values Form values
     * @return string
     */
    public function colorSelect($name, array $values)
    {
      $colors = $this->colorModel->getList();
      $html = $this->label(t('Color'), $name);
      $html .= $this->select($name, $colors, $values, array(), array('tabindex="4"'), 'color-picker');
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
        return '<label><input type="radio" name="'.$name.'" class="'.$class.'" value="'.$this->helper->text->e($value).'" '.($selected ? 'checked="checked"' : '').'> '.$this->helper->text->e($label).'</label>';
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
     * @param  string  $name        Field name
     * @param  string  $label       Form label
     * @param  string  $value       Form value
     * @param  boolean $checked     Field selected or not
     * @param  string  $class       CSS class
     * @param  array   $attributes
     * @return string
     */
    public function checkbox($name, $label, $value, $checked = false, $class = '', array $attributes = array())
    {
        $htmlAttributes = '';

        if ($checked) {
            $attributes['checked'] = 'checked';
        }

        foreach ($attributes as $attribute => $attributeValue) {
            $htmlAttributes .= sprintf('%s="%s"', $attribute, $this->helper->text->e($attributeValue));
        }

        return sprintf(
            '<label><input type="checkbox" name="%s" class="%s" value="%s" %s>&nbsp;%s</label>',
            $name,
            $class,
            $this->helper->text->e($value),
            $htmlAttributes,
            $this->helper->text->e($label)
        );
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
        return '<label for="form-'.$name.'" '.implode(' ', $attributes).'>'.$this->helper->text->e($label).'</label>';
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
        $html .= isset($values[$name]) ? $this->helper->text->e($values[$name]) : '';
        $html .= '</textarea>';
        $html .= $this->errorList($errors, $name);

        return $html;
    }

    /**
     * Display a markdown editor
     *
     * @access public
     * @param  string  $name     Field name
     * @param  array   $values   Form values
     * @param  array   $errors   Form errors
     * @param  array   $attributes
     * @return string
     */
    public function textEditor($name, $values = array(), array $errors = array(), array $attributes = array())
    {
        $params = array(
            'name' => $name,
            'css' => $this->errorClass($errors, $name),
            'required' => isset($attributes['required']) && $attributes['required'],
            'tabindex' => isset($attributes['tabindex']) ? $attributes['tabindex'] : '-1',
            'labelPreview' => t('Preview'),
            'previewUrl' => $this->helper->url->to('TaskAjaxController', 'preview'),
            'labelWrite' => t('Write'),
            'labelTitle' => t('Title'),
            'placeholder' => t('Write your text in Markdown'),
            'autofocus' => isset($attributes['autofocus']) && $attributes['autofocus'],
            'suggestOptions' => array(
                'triggers' => array(
                    '#' => $this->helper->url->to('TaskAjaxController', 'suggest', array('search' => 'SEARCH_TERM')),
                )
            ),
        );

        if (isset($values['project_id'])) {
            $params['suggestOptions']['triggers']['@'] = $this->helper->url->to('UserAjaxController', 'mention', array('project_id' => $values['project_id'], 'search' => 'SEARCH_TERM'));
        }

        $html = '<div class="js-text-editor" data-params=\''.json_encode($params, JSON_HEX_APOS).'\'>';
        $html .= '<script type="text/template">'.(isset($values[$name]) ? htmlspecialchars($values[$name], ENT_QUOTES, 'UTF-8', true) : '').'</script>';
        $html .= '</div>';
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
     * Date field
     *
     * @access public
     * @param  string $label
     * @param  string $name
     * @param  array  $values
     * @param  array  $errors
     * @param  array  $attributes
     * @return string
     */
    public function date($label, $name, array $values, array $errors = array(), array $attributes = array())
    {
        $userFormat = $this->dateParser->getUserDateFormat();
        $values = $this->dateParser->format($values, array($name), $userFormat);
        $attributes = array_merge(array('placeholder="'.date($userFormat).'"'), $attributes);

        return $this->helper->form->label($label, $name) .
            $this->helper->form->text($name, $values, $errors, $attributes, 'form-date');
    }

    /**
     * Datetime field
     *
     * @access public
     * @param  string $label
     * @param  string $name
     * @param  array  $values
     * @param  array  $errors
     * @param  array  $attributes
     * @return string
     */
    public function datetime($label, $name, array $values, array $errors = array(), array $attributes = array())
    {
        $userFormat = $this->dateParser->getUserDateTimeFormat();
        $values = $this->dateParser->format($values, array($name), $userFormat);
        $attributes = array_merge(array('placeholder="'.date($userFormat).'"'), $attributes);

        return $this->helper->form->label($label, $name) .
            $this->helper->form->text($name, $values, $errors, $attributes, 'form-datetime');
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
                $html .= '<li>'.$this->helper->text->e($error).'</li>';
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
            return 'value="'.$this->helper->text->e($values->$name).'"';
        }

        return isset($values[$name]) ? 'value="'.$this->helper->text->e($values[$name]).'"' : '';
    }
}
