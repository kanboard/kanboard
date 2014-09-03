<?php

namespace Helper;

use Core\Security;

function param_csrf()
{
    return '&amp;csrf_token='.Security::getCSRFToken();
}

function js($filename)
{
    return '<script type="text/javascript" src="'.$filename.'?'.filemtime($filename).'"></script>';
}

function css($filename)
{
    return '<link rel="stylesheet" href="'.$filename.'?'.filemtime($filename).'" media="screen">';
}

function template($name, array $args = array())
{
    $tpl = new \Core\Template;
    return $tpl->load($name, $args);
}

function is_current_user($user_id)
{
    return $_SESSION['user']['id'] == $user_id;
}

function is_admin()
{
    return $_SESSION['user']['is_admin'] == 1;
}

function get_username()
{
    return $_SESSION['user']['name'] ?: $_SESSION['user']['username'];
}

function get_user_id()
{
    return $_SESSION['user']['id'];
}

function parse($text)
{
    $text = markdown($text);
    $text = preg_replace('!#(\d+)!i', '<a href="?controller=task&action=show&task_id=$1">$0</a>', $text);
    return $text;
}

function markdown($text)
{
    require_once __DIR__.'/../vendor/Michelf/MarkdownExtra.inc.php';

    $parser = new \Michelf\MarkdownExtra;
    $parser->no_markup = true;
    $parser->no_entities = true;

    return $parser->transform($text);
}

function get_current_base_url()
{
    $url = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'];
    $url .= $_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443 ? '' : ':'.$_SERVER['SERVER_PORT'];
    $url .= dirname($_SERVER['PHP_SELF']) !== '/' ? dirname($_SERVER['PHP_SELF']).'/' : '/';

    return $url;
}

function escape($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
}

function flash($html)
{
    $data = '';

    if (isset($_SESSION['flash_message'])) {
        $data = sprintf($html, escape($_SESSION['flash_message']));
        unset($_SESSION['flash_message']);
    }

    return $data;
}

function flash_error($html)
{
    $data = '';

    if (isset($_SESSION['flash_error_message'])) {
        $data = sprintf($html, escape($_SESSION['flash_error_message']));
        unset($_SESSION['flash_error_message']);
    }

    return $data;
}

function format_bytes($size, $precision = 2)
{
    $base = log($size) / log(1024);
    $suffixes = array('', 'k', 'M', 'G', 'T');

    return round(pow(1024, $base - floor($base)), $precision).$suffixes[(int)floor($base)];
}

function get_host_from_url($url)
{
    return escape(parse_url($url, PHP_URL_HOST)) ?: $url;
}

function summary($value, $max_length = 85, $end = '[...]')
{
    $length = strlen($value);

    if ($length > $max_length) {
        return substr($value, 0, $max_length).' '.$end;
    }

    return $value;
}

function contains($haystack, $needle)
{
    return strpos($haystack, $needle) !== false;
}

function in_list($id, array $listing, $default_value = '?')
{
    if (isset($listing[$id])) {
        return escape($listing[$id]);
    }

    return $default_value;
}

function error_class(array $errors, $name)
{
    return ! isset($errors[$name]) ? '' : ' form-error';
}

function error_list(array $errors, $name)
{
    $html = '';

    if (isset($errors[$name])) {

        $html .= '<ul class="form-errors">';

        foreach ($errors[$name] as $error) {
            $html .= '<li>'.escape($error).'</li>';
        }

        $html .= '</ul>';
    }

    return $html;
}

function form_value($values, $name)
{
    if (isset($values->$name)) {
        return 'value="'.escape($values->$name).'"';
    }

    return isset($values[$name]) ? 'value="'.escape($values[$name]).'"' : '';
}

function form_csrf()
{
    return '<input type="hidden" name="csrf_token" value="'.Security::getCSRFToken().'"/>';
}

function form_hidden($name, $values = array())
{
    return '<input type="hidden" name="'.$name.'" id="form-'.$name.'" '.form_value($values, $name).'/>';
}

function form_default_select($name, array $options, $values = array(), array $errors = array(), $class = '')
{
    $options = array('' => '?') + $options;
    return form_select($name, $options, $values, $errors, $class);
}

function form_select($name, array $options, $values = array(), array $errors = array(), $class = '')
{
    $html = '<select name="'.$name.'" id="form-'.$name.'" class="'.$class.'">';

    foreach ($options as $id => $value) {

        $html .= '<option value="'.escape($id).'"';

        if (isset($values->$name) && $id == $values->$name) $html .= ' selected="selected"';
        if (isset($values[$name]) && $id == $values[$name]) $html .= ' selected="selected"';

        $html .= '>'.escape($value).'</option>';
    }

    $html .= '</select>';
    $html .= error_list($errors, $name);

    return $html;
}

function form_radios($name, array $options, array $values = array())
{
    $html = '';

    foreach ($options as $value => $label) {
        $html .= form_radio($name, $label, $value, isset($values[$name]) && $values[$name] == $value);
    }

    return $html;
}

function form_radio($name, $label, $value, $selected = false, $class = '')
{
    return '<label><input type="radio" name="'.$name.'" class="'.$class.'" value="'.escape($value).'" '.($selected ? 'selected="selected"' : '').'>'.escape($label).'</label>';
}

function form_checkbox($name, $label, $value, $checked = false, $class = '')
{
    return '<label><input type="checkbox" name="'.$name.'" class="'.$class.'" value="'.escape($value).'" '.($checked ? 'checked="checked"' : '').'>&nbsp;'.escape($label).'</label>';
}

function form_label($label, $name, array $attributes = array())
{
    return '<label for="form-'.$name.'" '.implode(' ', $attributes).'>'.escape($label).'</label>';
}

function form_textarea($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    $class .= error_class($errors, $name);

    $html = '<textarea name="'.$name.'" id="form-'.$name.'" class="'.$class.'" ';
    $html .= implode(' ', $attributes).'>';
    $html .= isset($values->$name) ? escape($values->$name) : isset($values[$name]) ? $values[$name] : '';
    $html .= '</textarea>';
    if (in_array('required', $attributes)) $html .= '<span class="form-required">*</span>';
    $html .= error_list($errors, $name);

    return $html;
}

function form_input($type, $name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    $class .= error_class($errors, $name);

    $html = '<input type="'.$type.'" name="'.$name.'" id="form-'.$name.'" '.form_value($values, $name).' class="'.$class.'" ';
    $html .= implode(' ', $attributes).'/>';
    if (in_array('required', $attributes)) $html .= '<span class="form-required">*</span>';
    $html .= error_list($errors, $name);

    return $html;
}

function form_text($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('text', $name, $values, $errors, $attributes, $class);
}

function form_password($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('password', $name, $values, $errors, $attributes, $class);
}

function form_email($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('email', $name, $values, $errors, $attributes, $class);
}

function form_date($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('date', $name, $values, $errors, $attributes, $class);
}

function form_number($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('number', $name, $values, $errors, $attributes, $class);
}

function form_numeric($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('text', $name, $values, $errors, $attributes, $class.' form-numeric');
}
