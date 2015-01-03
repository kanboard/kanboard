<?php

namespace Core;

use Pimple\Container;
use Parsedown;

/**
 * Template helpers
 *
 * @package core
 * @author  Frederic Guillot
 *
 * @property \Core\Session             $session
 * @property \Model\Acl                $acl
 * @property \Model\User               $user
 * @property \Model\UserSession        $userSession
 */
class Helper
{
    /**
     * Container instance
     *
     * @access protected
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Load automatically models
     *
     * @access public
     * @param  string    $name    Model name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->container[$name];
    }

    /**
     * Proxy cache helper for acl::isManagerActionAllowed()
     *
     * @access public
     * @param  integer   $project_id
     * @return boolean
     */
    public function isManager($project_id)
    {
        if ($this->userSession->isAdmin()) {
            return true;
        }

        return $this->container['memoryCache']->proxy('acl', 'isManagerActionAllowed', $project_id);
    }

    /**
     * Return the user full name
     *
     * @param  array    $user   User properties
     * @return string
     */
    public function getFullname(array $user = array())
    {
        return $this->user->getFullname(empty($user) ? $_SESSION['user'] : $user);
    }

    /**
     * HTML escaping
     *
     * @param  string   $value    Value to escape
     * @return string
     */
    public function e($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Add a Javascript asset
     *
     * @param  string   $filename   Filename
     * @return string
     */
    public function js($filename)
    {
        return '<script type="text/javascript" src="'.$filename.'?'.filemtime($filename).'"></script>';
    }

    /**
     * Add a stylesheet asset
     *
     * @param  string   $filename   Filename
     * @return string
     */
    public function css($filename)
    {
        return '<link rel="stylesheet" href="'.$filename.'?'.filemtime($filename).'" media="screen">';
    }

    /**
     * Display the form error class
     *
     * @param array   $errors   Error list
     * @param string  $name     Field name
     * @return string
     */
    public function errorClass(array $errors, $name)
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
    public function errorList(array $errors, $name)
    {
        $html = '';

        if (isset($errors[$name])) {

            $html .= '<ul class="form-errors">';

            foreach ($errors[$name] as $error) {
                $html .= '<li>'.$this->e($error).'</li>';
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
    public function formValue($values, $name)
    {
        if (isset($values->$name)) {
            return 'value="'.$this->e($values->$name).'"';
        }

        return isset($values[$name]) ? 'value="'.$this->e($values[$name]).'"' : '';
    }

    /**
     * Hidden CSRF token field
     *
     * @return string
     */
    public function formCsrf()
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
    public function formHidden($name, array $values = array())
    {
        return '<input type="hidden" name="'.$name.'" id="form-'.$name.'" '.$this->formValue($values, $name).'/>';
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
    public function formSelect($name, array $options, array $values = array(), array $errors = array(), $class = '')
    {
        $html = '<select name="'.$name.'" id="form-'.$name.'" class="'.$class.'">';

        foreach ($options as $id => $value) {

            $html .= '<option value="'.$this->e($id).'"';

            if (isset($values->$name) && $id == $values->$name) $html .= ' selected="selected"';
            if (isset($values[$name]) && $id == $values[$name]) $html .= ' selected="selected"';

            $html .= '>'.$this->e($value).'</option>';
        }

        $html .= '</select>';
        $html .= $this->errorList($errors, $name);

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
    public function formRadios($name, array $options, array $values = array())
    {
        $html = '';

        foreach ($options as $value => $label) {
            $html .= $this->formRadio($name, $label, $value, isset($values[$name]) && $values[$name] == $value);
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
    public function formRadio($name, $label, $value, $selected = false, $class = '')
    {
        return '<label><input type="radio" name="'.$name.'" class="'.$class.'" value="'.$this->e($value).'" '.($selected ? 'selected="selected"' : '').'>'.$this->e($label).'</label>';
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
    public function formCheckbox($name, $label, $value, $checked = false, $class = '')
    {
        return '<label><input type="checkbox" name="'.$name.'" class="'.$class.'" value="'.$this->e($value).'" '.($checked ? 'checked="checked"' : '').'>&nbsp;'.$this->e($label).'</label>';
    }

    /**
     * Display a form label
     *
     * @param  string  $name        Field name
     * @param  string  $label       Form label
     * @param  array   $attributes  HTML attributes
     * @return string
     */
    public function formLabel($label, $name, array $attributes = array())
    {
        return '<label for="form-'.$name.'" '.implode(' ', $attributes).'>'.$this->e($label).'</label>';
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
    public function formTextarea($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        $class .= $this->errorClass($errors, $name);

        $html = '<textarea name="'.$name.'" id="form-'.$name.'" class="'.$class.'" ';
        $html .= implode(' ', $attributes).'>';
        $html .= isset($values->$name) ? $this->e($values->$name) : isset($values[$name]) ? $values[$name] : '';
        $html .= '</textarea>';
        $html .= $this->errorList($errors, $name);

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
    public function formInput($type, $name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        $class .= $this->errorClass($errors, $name);

        $html = '<input type="'.$type.'" name="'.$name.'" id="form-'.$name.'" '.$this->formValue($values, $name).' class="'.$class.'" ';
        $html .= implode(' ', $attributes).'/>';
        if (in_array('required', $attributes)) $html .= '<span class="form-required">*</span>';
        $html .= $this->errorList($errors, $name);

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
    public function formText($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        return $this->formInput('text', $name, $values, $errors, $attributes, $class);
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
    public function formPassword($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        return $this->formInput('password', $name, $values, $errors, $attributes, $class);
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
    public function formEmail($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        return $this->formInput('email', $name, $values, $errors, $attributes, $class);
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
    public function formNumber($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        return $this->formInput('number', $name, $values, $errors, $attributes, $class);
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
    public function formNumeric($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
    {
        return $this->formInput('text', $name, $values, $errors, $attributes, $class.' form-numeric');
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
     * @param  boolean  $new_tab     Open the link in a new tab
     * @return string
     */
    public function a($label, $controller, $action, array $params = array(), $csrf = false, $class = '', $title = '', $new_tab = false)
    {
        return '<a href="'.$this->u($controller, $action, $params, $csrf).'" class="'.$class.'" title="'.$title.'" '.($new_tab ? 'target="_blank"' : '').'>'.$label.'</a>';
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
    public function u($controller, $action, array $params = array(), $csrf = false)
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
    public function paginate(array $pagination)
    {
        extract($pagination);

        if ($pagination['offset'] === 0 && ($total - $pagination['offset']) <= $limit) {
            return '';
        }

        $html = '<div class="pagination">';
        $html .= '<span class="pagination-previous">';

        if ($pagination['offset'] > 0) {
            $offset = $pagination['offset'] - $limit;
            $html .= $this->a('&larr; '.t('Previous'), $controller, $action, $params + compact('offset', 'order', 'direction'));
        }
        else {
            $html .= '&larr; '.t('Previous');
        }

        $html .= '</span>';
        $html .= '<span class="pagination-next">';

        if (($total - $pagination['offset']) > $limit) {
            $offset = $pagination['offset'] + $limit;
            $html .= $this->a(t('Next').' &rarr;', $controller, $action, $params + compact('offset', 'order', 'direction'));
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
    public function order($label, $column, array $pagination)
    {
        extract($pagination);

        $prefix = '';

        if ($order === $column) {
            $prefix = $direction === 'DESC' ? '&#9660; ' : '&#9650; ';
            $direction = $direction === 'DESC' ? 'ASC' : 'DESC';
        }

        $order = $column;

        return $prefix.$this->a($label, $controller, $action, $params + compact('offset', 'order', 'direction'));
    }

    /**
     * Markdown transformation
     *
     * @param  string    $text     Markdown content
     * @param  array     $link     Link parameters for replacement
     * @return string
     */
    public function markdown($text, array $link = array())
    {
        $html = Parsedown::instance()
                    ->setMarkupEscaped(true) # escapes markup (HTML)
                    ->text($text);

        // Replace task #123 by a link to the task
        if (! empty($link) && preg_match_all('!#(\d+)!i', $html, $matches, PREG_SET_ORDER)) {

            foreach ($matches as $match) {

                $html = str_replace(
                    $match[0],
                    $this->a($match[0], $link['controller'], $link['action'], $link['params'] + array('task_id' => $match[1])),
                    $html
                );
            }
        }

        return $html;
    }

    /**
     * Get the current URL without the querystring
     *
     * @return string
     */
    public function getCurrentBaseUrl()
    {
        $url = Request::isHTTPS() ? 'https://' : 'http://';
        $url .= $_SERVER['SERVER_NAME'];
        $url .= $_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443 ? '' : ':'.$_SERVER['SERVER_PORT'];
        $url .= dirname($_SERVER['PHP_SELF']) !== '/' ? dirname($_SERVER['PHP_SELF']).'/' : '/';

        return $url;
    }

    /**
     * Dispplay the flash session message
     *
     * @param  string   $html   HTML wrapper
     * @return string
     */
    public function flash($html)
    {
        return $this->flashMessage('flash_message', $html);
    }

    /**
     * Display the flash session error message
     *
     * @param  string    $html    HTML wrapper
     * @return string
     */
    public function flashError($html)
    {
        return $this->flashMessage('flash_error_message', $html);
    }

    /**
     * Fetch and remove a flash session message
     *
     * @access private
     * @param  string    $name    Message name
     * @param  string    $html    HTML wrapper
     * @return string
     */
    private function flashMessage($name, $html)
    {
        $data = '';

        if (isset($this->session[$name])) {
            $data = sprintf($html, $this->e($this->session[$name]));
            unset($this->session[$name]);
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
    public function formatBytes($size, $precision = 2)
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
    public function summary($value, $max_length = 85, $end = '[...]')
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
    public function contains($haystack, $needle)
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
    public function inList($id, array $listing, $default_value = '?')
    {
        if (isset($listing[$id])) {
            return $this->e($listing[$id]);
        }

        return $default_value;
    }
}
