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
        $success_message = $this->flash->getMessage('success');
        $failure_message = $this->flash->getMessage('failure');

        if (! empty($success_message)) {
            return '<div class="alert alert-success alert-fade-out">'.$this->helper->e($success_message).'</div>';
        }

        if (! empty($failure_message)) {
            return '<div class="alert alert-error">'.$this->helper->e($failure_message).'</div>';
        }

        return '';
    }
}
