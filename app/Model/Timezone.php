<?php

namespace Kanboard\Model;

/**
 * Class Timezone
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class Timezone extends Base
{
    /**
     * Get available timezones
     *
     * @access public
     * @param  boolean   $prepend  Prepend a default value
     * @return array
     */
    public function getTimezones($prepend = false)
    {
        $timezones = timezone_identifiers_list();
        $listing = array_combine(array_values($timezones), $timezones);

        if ($prepend) {
            return array('' => t('Application default')) + $listing;
        }

        return $listing;
    }

    /**
     * Get current timezone
     *
     * @access public
     * @return string
     */
    public function getCurrentTimezone()
    {
        if ($this->userSession->isLogged() && ! empty($this->sessionStorage->user['timezone'])) {
            return $this->sessionStorage->user['timezone'];
        }

        return $this->config->get('application_timezone', 'UTC');
    }

    /**
     * Set timezone
     *
     * @access public
     */
    public function setCurrentTimezone()
    {
        date_default_timezone_set($this->getCurrentTimezone());
    }
}
