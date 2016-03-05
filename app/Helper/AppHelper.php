<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Application Helper
 *
 * @package helper
 * @author  Frederic Guillot
 */
class AppHelper extends Base
{
    /**
     * Get config variable
     *
     * @access public
     * @param  string $param
     * @param  mixed  $default_value
     * @return mixed
     */
    public function config($param, $default_value = '')
    {
        return $this->config->get($param, $default_value);
    }

    /**
     * Make sidebar menu active
     *
     * @access public
     * @param  string $controller
     * @param  string $action
     * @param  string $plugin
     * @return string
     */
    public function checkMenuSelection($controller, $action = '', $plugin = '')
    {
        $result = strtolower($this->getRouterController()) === strtolower($controller);

        if ($result && $action !== '') {
            $result = strtolower($this->getRouterAction()) === strtolower($action);
        }

        if ($result && $plugin !== '') {
            $result = strtolower($this->getPluginName()) === strtolower($plugin);
        }

        return $result ? 'class="active"' : '';
    }

    /**
     * Get plugin name from route
     *
     * @access public
     * @return string
     */
    public function getPluginName()
    {
        return $this->router->getPlugin();
    }

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
            return '<div class="alert alert-success alert-fade-out">'.$this->helper->text->e($success_message).'</div>';
        }

        if (! empty($failure_message)) {
            return '<div class="alert alert-error">'.$this->helper->text->e($failure_message).'</div>';
        }

        return '';
    }
}
