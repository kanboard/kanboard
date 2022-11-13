<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Url Helper
 *
 * @package helper
 * @author  Frederic Guillot
 */
class UrlHelper extends Base
{
    private $base = '';
    private $directory = '';

    /**
     * Helper to generate a link to the documentation
     *
     * @access public
     * @param  string  $label
     * @param  string  $file
     * @return string
     */
    public function doc($label, $file = '')
    {
        $url = sprintf(DOCUMENTATION_URL_PATTERN, $file);
        return sprintf('<a href="%s" target="_blank">%s</a>', $url, $label);
    }

    /**
     * Button Link Element
     *
     * @access public
     * @param  string  $icon       Font-Awesome icon
     * @param  string  $label      Link label
     * @param  string  $controller Controller name
     * @param  string  $action     Action name
     * @param  array   $params     Url parameters
     * @param  string  $class      CSS class attribute
     * @return string
     */
    public function button($icon, $label, $controller, $action, array $params = array(), $class = '')
    {
        $html = '<i class="fa fa-'.$icon.' fa-fw"></i> '.$label;
        $class = 'btn '.$class;
        return $this->link($html, $controller, $action, $params, false, $class);
    }

    /**
     * Link element with icon
     *
     * @access public
     * @param  string  $icon        Icon name
     * @param  string  $label       Link label
     * @param  string  $controller  Controller name
     * @param  string  $action      Action name
     * @param  array   $params      Url parameters
     * @param  boolean $csrf        Add a CSRF token
     * @param  string  $class       CSS class attribute
     * @param  string  $title       Link title
     * @param  boolean $newTab      Open the link in a new tab
     * @param  string  $anchor      Link Anchor
     * @param  bool    $absolute
     * @return string
     */
    public function icon($icon, $label, $controller, $action, array $params = array(), $csrf = false, $class = '', $title = '', $newTab = false, $anchor = '', $absolute = false)
    {
        $html = '<i class="fa fa-fw fa-'.$icon.'" aria-hidden="true"></i>'.$label;
        return $this->helper->url->link($html, $controller, $action, $params, $csrf, $class, $title, $newTab, $anchor, $absolute);
    }

    /**
     * Link element
     *
     * @access public
     * @param  string  $label       Link label
     * @param  string  $controller  Controller name
     * @param  string  $action      Action name
     * @param  array   $params      Url parameters
     * @param  boolean $csrf        Add a CSRF token
     * @param  string  $class       CSS class attribute
     * @param  string  $title       Link title
     * @param  boolean $newTab      Open the link in a new tab
     * @param  string  $anchor      Link Anchor
     * @param  bool    $absolute
     * @return string
     */
    public function link($label, $controller, $action, array $params = array(), $csrf = false, $class = '', $title = '', $newTab = false, $anchor = '', $absolute = false)
    {
        return '<a href="'.$this->href($controller, $action, $params, $csrf, $anchor, $absolute).'" class="'.$class.'" title=\''.$title.'\' '.($newTab ? 'target="_blank"' : '').'>'.$label.'</a>';
    }

    /**
     * Absolute link
     *
     * @param  string $label
     * @param  string $controller
     * @param  string $action
     * @param  array $params
     * @return string
     */
    public function absoluteLink($label, $controller, $action, array $params = array())
    {
        return $this->link($label, $controller, $action, $params, false, '', '', true, '', true);
    }

    /**
     * HTML Hyperlink
     *
     * @access public
     * @param  string   $controller  Controller name
     * @param  string   $action      Action name
     * @param  array    $params      Url parameters
     * @param  boolean  $csrf        Add a CSRF token
     * @param  string   $anchor      Link Anchor
     * @param  boolean  $absolute    Absolute or relative link
     * @return string
     */
    public function href($controller, $action, array $params = array(), $csrf = false, $anchor = '', $absolute = false)
    {
        return $this->build('&amp;', $controller, $action, $params, $csrf, $anchor, $absolute);
    }

    /**
     * Generate controller/action url
     *
     * @access public
     * @param  string   $controller  Controller name
     * @param  string   $action      Action name
     * @param  array    $params      Url parameters
     * @param  string   $anchor      Link Anchor
     * @param  boolean  $absolute    Absolute or relative link
     * @return string
     */
    public function to($controller, $action, array $params = array(), $anchor = '', $absolute = false)
    {
        return $this->build('&', $controller, $action, $params, false, $anchor, $absolute);
    }

    /**
     * Get application base url
     *
     * @access public
     * @return string
     */
    public function base()
    {
        if (empty($this->base)) {
            $this->base = $this->configModel->get('application_url') ?: $this->server();
        }

        return $this->base;
    }

    /**
     * Get application base directory
     *
     * @access public
     * @return string
     */
    public function dir()
    {
        if ($this->directory === '' && $this->request->getMethod() !== '') {
            if (defined('KANBOARD_URL') && strlen(KANBOARD_URL) > 0) {
                $this->directory = parse_url(KANBOARD_URL, PHP_URL_PATH);
            } else {
                $this->directory = str_replace('\\', '/', dirname($this->request->getServerVariable('PHP_SELF')));
                $this->directory = $this->directory !== '/' ? $this->directory.'/' : '/';
                $this->directory = str_replace('//', '/', $this->directory);
            }
        }

        return $this->directory;
    }

    /**
     * Get current server base url
     *
     * @access public
     * @return string
     */
    public function server()
    {
        if ($this->request->getServerVariable('SERVER_NAME') === '') {
            return 'http://localhost/';
        }

        $url = $this->request->isHTTPS() ? 'https://' : 'http://';
        $url .= $this->request->getServerVariable('SERVER_NAME');
        $url .= $this->request->getServerVariable('SERVER_PORT') == 80 || $this->request->getServerVariable('SERVER_PORT') == 443 ? '' : ':'.$this->request->getServerVariable('SERVER_PORT');
        $url .= $this->dir() ?: '/';

        return $url;
    }

    /**
     * Build relative url
     *
     * @access protected
     * @param  string   $separator   Querystring argument separator
     * @param  string   $controller  Controller name
     * @param  string   $action      Action name
     * @param  array    $params      Url parameters
     * @param  boolean  $csrf        Add a CSRF token
     * @param  string   $anchor      Link Anchor
     * @param  boolean  $absolute    Absolute or relative link
     * @return string
     */
    protected function build($separator, $controller, $action, array $params = array(), $csrf = false, $anchor = '', $absolute = false)
    {
        $path = $this->route->findUrl($controller, $action, $params);
        $qs = array();

        if (empty($path)) {
            $qs['controller'] = $controller;
            $qs['action'] = $action;
            $qs += $params;
        } else {
            unset($params['plugin']);
        }

        if ($csrf) {
            $qs['csrf_token'] = $this->token->getCSRFToken();
        }

        if (! empty($qs)) {
            $path .= '?'.http_build_query($qs, '', $separator);
        }

        return ($absolute ? $this->base() : $this->dir()).$path.(empty($anchor) ? '' : '#'.$anchor);
    }
}
