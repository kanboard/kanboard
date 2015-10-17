<?php

namespace Kanboard\Helper;

/**
 * Application helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class App extends \Kanboard\Core\Base
{
    /**
     * Get router controller
     *
     * @access public
     * @return string
     */
    public function getRouterController()
    {
        return $this->router->getController();
    }

    /**
     * Get router action
     *
     * @access public
     * @return string
     */
    public function getRouterAction()
    {
        return $this->router->getAction();
    }

    /**
     * Get javascript language code
     *
     * @access public
     * @return string
     */
    public function jsLang()
    {
        return $this->config->getJsLanguageCode();
    }

    /**
     * Get current timezone
     *
     * @access public
     * @return string
     */
    public function getTimezone()
    {
        return $this->config->getCurrentTimezone();
    }

    /**
     * Get session flash message
     *
     * @access public
     * @return string
     */
    public function flashMessage()
    {
        $html = '';

        if (isset($this->session['flash_message'])) {
            $html = '<div class="alert alert-success alert-fade-out">'.$this->helper->e($this->session['flash_message']).'</div>';
            unset($this->session['flash_message']);
            unset($this->session['flash_error_message']);
        } elseif (isset($this->session['flash_error_message'])) {
            $html = '<div class="alert alert-error">'.$this->helper->e($this->session['flash_error_message']).'</div>';
            unset($this->session['flash_message']);
            unset($this->session['flash_error_message']);
        }

        return $html;
    }
}
