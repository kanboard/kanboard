<?php

namespace Helper;

use Core\Request;
use Core\Security;

/**
 * Url helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class Url extends \Core\Base
{
    /**
     * HTML Link tag
     *
     * @access public
     * @param  string   $label       Link label
     * @param  string   $controller  Controller name
     * @param  string   $action      Action name
     * @param  array    $params      Url parameters
     * @param  boolean  $csrf        Add a CSRF token
     * @param  string   $class       CSS class attribute
     * @param  boolean  $new_tab     Open the link in a new tab
     * @param  string   $anchor      Link Anchor
     * @return string
     */
    public function link($label, $controller, $action, array $params = array(), $csrf = false, $class = '', $title = '', $new_tab = false, $anchor = '')
    {
        return '<a href="'.$this->href($controller, $action, $params, $csrf, $anchor).'" class="'.$class.'" title="'.$title.'" '.($new_tab ? 'target="_blank"' : '').'>'.$label.'</a>';
    }

    /**
     * Hyperlink
     *
     * @access public
     * @param  string   $controller  Controller name
     * @param  string   $action      Action name
     * @param  array    $params      Url parameters
     * @param  boolean  $csrf        Add a CSRF token
     * @param  string   $anchor      Link Anchor
     * @return string
     */
    public function href($controller, $action, array $params = array(), $csrf = false, $anchor = '')
    {
        $values = array(
            'controller' => $controller,
            'action' => $action,
        );

        if ($csrf) {
            $params['csrf_token'] = Security::getCSRFToken();
        }

        $values += $params;

        return '?'.http_build_query($values, '', '&amp;').(empty($anchor) ? '' : '#'.$anchor);
    }

    /**
     * Generate controller/action url
     *
     * @access public
     * @param  string   $controller  Controller name
     * @param  string   $action      Action name
     * @param  array    $params      Url parameters
     * @return string
     */
    public function to($controller, $action, array $params = array())
    {
        $values = array(
            'controller' => $controller,
            'action' => $action,
        );

        $values += $params;

        return '?'.http_build_query($values, '', '&');
    }

    /**
     * Get application base url
     *
     * @access public
     * @return string
     */
    public function base()
    {
        return $this->config->get('application_url') ?: $this->server();
    }

    /**
     * Get current server base url
     *
     * @access public
     * @return string
     */
    public function server()
    {
        if (empty($_SERVER['SERVER_NAME'])) {
            return 'http://localhost/';
        }

        $self = str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));

        $url = Request::isHTTPS() ? 'https://' : 'http://';
        $url .= $_SERVER['SERVER_NAME'];
        $url .= $_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443 ? '' : ':'.$_SERVER['SERVER_PORT'];
        $url .= $self !== '/' ? $self.'/' : '/';

        return $url;
    }
}
