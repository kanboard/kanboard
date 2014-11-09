<?php

namespace Helper;

/**
 * Template helpers
 *
 */
use Core\Security;
use Core\Template;
use Core\Tool;
use Parsedown;

/**
 * Append a CSRF token to a query string
 *
 * @return string
 */
function param_csrf()
{
    return '&amp;csrf_token='.Security::getCSRFToken();
}

/**
 * Add a Javascript asset
 *
 * @param  string   $filename   Filename
 * @return string
 */
function js($filename)
{
    return '<script type="text/javascript" src="'.$filename.'?'.filemtime($filename).'"></script>';
}

/**
 * Add a stylesheet asset
 *
 * @param  string   $filename   Filename
 * @return string
 */
function css($filename)
{
    return '<link rel="stylesheet" href="'.$filename.'?'.filemtime($filename).'" media="screen">';
}

/**
 * Load a template
 *
 * @param  string    $name    Template name
 * @param  array     $args    Template parameters
 * @return string
 */
function template($name, array $args = array())
{
    $tpl = new Template;
    return $tpl->load($name, $args);
}

/**
 * Check if the given user_id is the connected user
 *
 * @param  integer   $user_id   User id
 * @return boolean
 */
function is_current_user($user_id)
{
    return $_SESSION['user']['id'] == $user_id;
}

/**
 * Check if the current user is administrator
 *
 * @return boolean
 */
function is_admin()
{
    return $_SESSION['user']['is_admin'] == 1;
}

/**
 * Return true if the user can configure the project (project are previously filtered)
 *
 * @return boolean
 */
function is_project_admin(array $project)
{
    return is_admin() || $project['is_private'] == 1;
}

/**
 * Return the username
 *
 * @param  array    $user   User properties (optional)
 * @return string
 */
function get_username(array $user = array())
{
    return ! empty($user) ? ($user['name'] ?: $user['username'])
                : ($_SESSION['user']['name'] ?: $_SESSION['user']['username']);
}

/**
 * Get the current user id
 *
 * @return integer
 */
function get_user_id()
{
    return $_SESSION['user']['id'];
}

/**
 * Markdown transformation
 *
 * @param  string    $text     Markdown content
 * @param  array     $link     Link parameters for replacement
 * @return string
 */
function markdown($text, array $link = array('controller' => 'task', 'action' => 'show', 'params' => array()))
{
    $html = Parsedown::instance()
                ->setMarkupEscaped(true) # escapes markup (HTML)
                ->text($text);

    // Replace task #123 by a link to the task
    $html = preg_replace_callback('!#(\d+)!i', function($matches) use ($link) {
        return a(
            $matches[0],
            $link['controller'],
            $link['action'],
            $link['params'] + array('task_id' => $matches[1])
        );
    }, $html);

    return $html;
}

/**
 * Get the current URL without the querystring
 *
 * @return string
 */
function get_current_base_url()
{
    $url = Tool::isHTTPS() ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'];
    $url .= $_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443 ? '' : ':'.$_SERVER['SERVER_PORT'];
    $url .= dirname($_SERVER['PHP_SELF']) !== '/' ? dirname($_SERVER['PHP_SELF']).'/' : '/';

    return $url;
}

/**
 * HTML escaping
 *
 * @param  string   $value    Value to escape
 * @return string
 */
function escape($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
}

/**
 * Dispplay the flash session message
 *
 * @param  string   $html   HTML wrapper
 * @return string
 */
function flash($html)
{
    $data = '';

    if (isset($_SESSION['flash_message'])) {
        $data = sprintf($html, escape($_SESSION['flash_message']));
        unset($_SESSION['flash_message']);
    }

    return $data;
}

/**
 * Display the flash session error message
 *
 * @param  string    $html    HTML wrapper
 * @return string
 */
function flash_error($html)
{
    $data = '';

    if (isset($_SESSION['flash_error_message'])) {
        $data = sprintf($html, escape($_SESSION['flash_error_message']));
        unset($_SESSION['flash_error_message']);
    }

    return $data;
}

/**
 * Format a file size
 *
 * @param  integer  $size        Size in bytes
 * @param  integer  $precision   Precision
 * @return string
 */
function format_bytes($size, $precision = 2)
{
    $base = log($size) / log(1024);
    $suffixes = array('', 'k', 'M', 'G', 'T');

    return round(pow(1024, $base - floor($base)), $precision).$suffixes[(int)floor($base)];
}

/**
 * Truncate a long text
 *
 * @param  string    $value         Text
 * @param  integer   $max_length    Max Length
 * @param  string    $end           Text end
 * @return string
 */
function summary($value, $max_length = 85, $end = '[...]')
{
    $length = strlen($value);

    if ($length > $max_length) {
        return substr($value, 0, $max_length).' '.$end;
    }

    return $value;
}

/**
 * Return true if needle is contained in the haystack
 *
 * @param  string   $haystack   Haystack
 * @param  string   $needle     Needle
 * @return boolean
 */
function contains($haystack, $needle)
{
    return strpos($haystack, $needle) !== false;
}

/**
 * Return a value from a dictionary
 *
 * @param  mixed   $id              Key
 * @param  array   $listing         Dictionary
 * @param  string  $default_value   Value displayed when the key doesn't exists
 * @return string
 */
function in_list($id, array $listing, $default_value = '?')
{
    if (isset($listing[$id])) {
        return escape($listing[$id]);
    }

    return $default_value;
}

/**
 * Display the form error class
 *
 * @param array   $errors   Error list
 * @param string  $name     Field name
 * @return string
 */
function error_class(array $errors, $name)
{
    return ! isset($errors[$name]) ? '' : ' form-error';
}

/**
 * Display a list of form errors
 *
 * @param array   $errors   List of errors
 * @param string  $name     Field name
 * @return string
 */
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

/**
 * Get an escaped form value
 *
 * @param  mixed  $values  Values
 * @param  string $name    Field name
 * @return string
 */
function form_value($values, $name)
{
    if (isset($values->$name)) {
        return 'value="'.escape($values->$name).'"';
    }

    return isset($values[$name]) ? 'value="'.escape($values[$name]).'"' : '';
}

/**
 * Hidden CSRF token field
 *
 * @return string
 */
function form_csrf()
{
    return '<input type="hidden" name="csrf_token" value="'.Security::getCSRFToken().'"/>';
}

/**
 * Display a hidden form field
 *
 * @param  string  $name    Field name
 * @param  array   $values  Form values
 * @return string
 */
function form_hidden($name, array $values = array())
{
    return '<input type="hidden" name="'.$name.'" id="form-'.$name.'" '.form_value($values, $name).'/>';
}

/**
 * Display a select field
 *
 * @param  string  $name     Field name
 * @param  array   $options  Options
 * @param  array   $values   Form values
 * @param  array   $errors   Form errors
 * @param  string  $class    CSS class
 * @return string
 */
function form_select($name, array $options, array $values = array(), array $errors = array(), $class = '')
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

/**
 * Display a radio field group
 *
 * @param  string  $name     Field name
 * @param  array   $options  Options
 * @param  array   $values   Form values
 * @return string
 */
function form_radios($name, array $options, array $values = array())
{
    $html = '';

    foreach ($options as $value => $label) {
        $html .= form_radio($name, $label, $value, isset($values[$name]) && $values[$name] == $value);
    }

    return $html;
}

/**
 * Display a radio field
 *
 * @param  string  $name      Field name
 * @param  string  $label     Form label
 * @param  string  $value     Form value
 * @param  boolean $selected  Field selected or not
 * @param  string  $class     CSS class
 * @return string
 */
function form_radio($name, $label, $value, $selected = false, $class = '')
{
    return '<label><input type="radio" name="'.$name.'" class="'.$class.'" value="'.escape($value).'" '.($selected ? 'selected="selected"' : '').'>'.escape($label).'</label>';
}

/**
 * Display a checkbox field
 *
 * @param  string  $name      Field name
 * @param  string  $label     Form label
 * @param  string  $value     Form value
 * @param  boolean $checked   Field selected or not
 * @param  string  $class     CSS class
 * @return string
 */
function form_checkbox($name, $label, $value, $checked = false, $class = '')
{
    return '<label><input type="checkbox" name="'.$name.'" class="'.$class.'" value="'.escape($value).'" '.($checked ? 'checked="checked"' : '').'>&nbsp;'.escape($label).'</label>';
}

/**
 * Display a form label
 *
 * @param  string  $name        Field name
 * @param  string  $label       Form label
 * @param  array   $attributes  HTML attributes
 * @return string
 */
function form_label($label, $name, array $attributes = array())
{
    return '<label for="form-'.$name.'" '.implode(' ', $attributes).'>'.escape($label).'</label>';
}

/**
 * Display a textarea
 *
 * @param  string  $name        Field name
 * @param  array   $values      Form values
 * @param  array   $errors      Form errors
 * @param  array   $attributes  HTML attributes
 * @param  string  $class       CSS class
 * @return string
 */
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

/**
 * Display a input field
 *
 * @param  string  $type        HMTL input tag type
 * @param  string  $name        Field name
 * @param  array   $values      Form values
 * @param  array   $errors      Form errors
 * @param  array   $attributes  HTML attributes
 * @param  string  $class       CSS class
 * @return string
 */
function form_input($type, $name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    $class .= error_class($errors, $name);

    $html = '<input type="'.$type.'" name="'.$name.'" id="form-'.$name.'" '.form_value($values, $name).' class="'.$class.'" ';
    $html .= implode(' ', $attributes).'/>';
    if (in_array('required', $attributes)) $html .= '<span class="form-required">*</span>';
    $html .= error_list($errors, $name);

    return $html;
}

/**
 * Display a text field
 *
 * @param  string  $name        Field name
 * @param  array   $values      Form values
 * @param  array   $errors      Form errors
 * @param  array   $attributes  HTML attributes
 * @param  string  $class       CSS class
 * @return string
 */
function form_text($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('text', $name, $values, $errors, $attributes, $class);
}

/**
 * Display a password field
 *
 * @param  string  $name        Field name
 * @param  array   $values      Form values
 * @param  array   $errors      Form errors
 * @param  array   $attributes  HTML attributes
 * @param  string  $class       CSS class
 * @return string
 */
function form_password($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('password', $name, $values, $errors, $attributes, $class);
}

/**
 * Display an email field
 *
 * @param  string  $name        Field name
 * @param  array   $values      Form values
 * @param  array   $errors      Form errors
 * @param  array   $attributes  HTML attributes
 * @param  string  $class       CSS class
 * @return string
 */
function form_email($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('email', $name, $values, $errors, $attributes, $class);
}

/**
 * Display a date field
 *
 * @param  string  $name        Field name
 * @param  array   $values      Form values
 * @param  array   $errors      Form errors
 * @param  array   $attributes  HTML attributes
 * @param  string  $class       CSS class
 * @return string
 */
function form_date($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('date', $name, $values, $errors, $attributes, $class);
}

/**
 * Display a number field
 *
 * @param  string  $name        Field name
 * @param  array   $values      Form values
 * @param  array   $errors      Form errors
 * @param  array   $attributes  HTML attributes
 * @param  string  $class       CSS class
 * @return string
 */
function form_number($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('number', $name, $values, $errors, $attributes, $class);
}

/**
 * Display a numeric field (allow decimal number)
 *
 * @param  string  $name        Field name
 * @param  array   $values      Form values
 * @param  array   $errors      Form errors
 * @param  array   $attributes  HTML attributes
 * @param  string  $class       CSS class
 * @return string
 */
function form_numeric($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('text', $name, $values, $errors, $attributes, $class.' form-numeric');
}

/**
 * Link
 *
 * a('link', 'task', 'show', array('task_id' => $task_id))
 *
 * @param  string   $label       Link label
 * @param  string   $controller  Controller name
 * @param  string   $action      Action name
 * @param  array    $params      Url parameters
 * @param  boolean  $csrf        Add a CSRF token
 * @param  string   $class       CSS class attribute
 * @return string
 */
function a($label, $controller, $action, array $params = array(), $csrf = false, $class = '', $title = '')
{
    return '<a href="'.u($controller, $action, $params, $csrf).'" class="'.$class.'" title="'.$title.'">'.$label.'</a>';
}

/**
 * URL query string
 *
 * u('task', 'show', array('task_id' => $task_id))
 *
 * @param  string   $controller  Controller name
 * @param  string   $action      Action name
 * @param  array    $params      Url parameters
 * @param  boolean  $csrf        Add a CSRF token
 * @return string
 */
function u($controller, $action, array $params = array(), $csrf = false)
{
    $html = '?controller='.$controller.'&amp;action='.$action;

    if ($csrf) {
        $params['csrf_token'] = Security::getCSRFToken();
    }

    foreach ($params as $key => $value) {
        $html .= '&amp;'.$key.'='.$value;
    }

    return $html;
}

/**
 * Pagination links
 *
 * @param  array    $pagination    Pagination information
 * @return string
 */
function paginate(array $pagination)
{
    extract($pagination);

    $html = '<div id="pagination">';
    $html .= '<span id="pagination-previous">';

    if ($pagination['offset'] > 0) {
        $offset = $pagination['offset'] - $limit;
        $html .= a('&larr; '.t('Previous'), $controller, $action, $params + compact('offset', 'order', 'direction'));
    }
    else {
        $html .= '&larr; '.t('Previous');
    }

    $html .= '</span>';
    $html .= '<span id="pagination-next">';

    if (($total - $pagination['offset']) > $limit) {
        $offset = $pagination['offset'] + $limit;
        $html .= a(t('Next').' &rarr;', $controller, $action, $params + compact('offset', 'order', 'direction'));
    }
    else {
        $html .= t('Next').' &rarr;';
    }

    $html .= '</span>';
    $html .= '</div>';

    return $html;
}

/**
 * Column sorting (work with pagination)
 *
 * @param  string   $label         Column title
 * @param  string   $column        SQL column name
 * @param  array    $pagination    Pagination information
 * @return string
 */
function order($label, $column, array $pagination)
{
    extract($pagination);

    $prefix = '';

    if ($order === $column) {
        $prefix = $direction === 'DESC' ? '&#9660; ' : '&#9650; ';
        $direction = $direction === 'DESC' ? 'ASC' : 'DESC';
    }

    $order = $column;

    return $prefix.a($label, $controller, $action, $params + compact('offset', 'order', 'direction'));
}
