<?php

namespace Kanboard\Model;

use Kanboard\Core\Translator;
use Kanboard\Core\Security\Token;
use Kanboard\Core\Session;

/**
 * Config model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Config extends Setting
{
    /**
     * Get available currencies
     *
     * @access public
     * @return array
     */
    public function getCurrencies()
    {
        return array(
            'USD' => t('USD - US Dollar'),
            'EUR' => t('EUR - Euro'),
            'GBP' => t('GBP - British Pound'),
            'CHF' => t('CHF - Swiss Francs'),
            'CAD' => t('CAD - Canadian Dollar'),
            'AUD' => t('AUD - Australian Dollar'),
            'NZD' => t('NZD - New Zealand Dollar'),
            'INR' => t('INR - Indian Rupee'),
            'JPY' => t('JPY - Japanese Yen'),
            'RSD' => t('RSD - Serbian dinar'),
            'SEK' => t('SEK - Swedish Krona'),
            'NOK' => t('NOK - Norwegian Krone'),
        );
    }

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
     * Get available languages
     *
     * @access public
     * @param  boolean   $prepend  Prepend a default value
     * @return array
     */
    public function getLanguages($prepend = false)
    {
        // Sorted by value
        $languages = array(
            'id_ID' => 'Bahasa Indonesia',
            'cs_CZ' => 'Čeština',
            'da_DK' => 'Dansk',
            'de_DE' => 'Deutsch',
            'en_US' => 'English',
            'es_ES' => 'Español',
            'fr_FR' => 'Français',
            'it_IT' => 'Italiano',
            'hu_HU' => 'Magyar',
            'nl_NL' => 'Nederlands',
            'nb_NO' => 'Norsk',
            'pl_PL' => 'Polski',
            'pt_PT' => 'Português',
            'pt_BR' => 'Português (Brasil)',
            'ru_RU' => 'Русский',
            'sr_Latn_RS' => 'Srpski',
            'fi_FI' => 'Suomi',
            'sv_SE' => 'Svenska',
            'tr_TR' => 'Türkçe',
            'zh_CN' => '中文(简体)',
            'ja_JP' => '日本語',
            'th_TH' => 'ไทย',
        );

        if ($prepend) {
            return array('' => t('Application default')) + $languages;
        }

        return $languages;
    }

    /**
     * Get javascript language code
     *
     * @access public
     * @return string
     */
    public function getJsLanguageCode()
    {
        $languages = array(
            'cs_CZ' => 'cz',
            'da_DK' => 'da',
            'de_DE' => 'de',
            'en_US' => 'en',
            'es_ES' => 'es',
            'fr_FR' => 'fr',
            'it_IT' => 'it',
            'hu_HU' => 'hu',
            'nl_NL' => 'nl',
            'nb_NO' => 'nb',
            'pl_PL' => 'pl',
            'pt_PT' => 'pt',
            'pt_BR' => 'pt-br',
            'ru_RU' => 'ru',
            'sr_Latn_RS' => 'sr',
            'fi_FI' => 'fi',
            'sv_SE' => 'sv',
            'tr_TR' => 'tr',
            'zh_CN' => 'zh-cn',
            'ja_JP' => 'ja',
            'th_TH' => 'th',
            'id_ID' => 'id'
        );

        $lang = $this->getCurrentLanguage();

        return isset($languages[$lang]) ? $languages[$lang] : 'en';
    }

    /**
     * Get current language
     *
     * @access public
     * @return string
     */
    public function getCurrentLanguage()
    {
        if ($this->userSession->isLogged() && ! empty($this->session['user']['language'])) {
            return $this->session['user']['language'];
        }

        return $this->get('application_language', 'en_US');
    }

    /**
     * Get a config variable from the session or the database
     *
     * @access public
     * @param  string   $name            Parameter name
     * @param  string   $default_value   Default value of the parameter
     * @return string
     */
    public function get($name, $default_value = '')
    {
        if (! Session::isOpen()) {
            return $this->getOption($name, $default_value);
        }

        // Cache config in session
        if (! isset($this->session['config'][$name])) {
            $this->session['config'] = $this->getAll();
        }

        if (! empty($this->session['config'][$name])) {
            return $this->session['config'][$name];
        }

        return $default_value;
    }

    /**
     * Reload settings in the session and the translations
     *
     * @access public
     */
    public function reload()
    {
        $this->session['config'] = $this->getAll();
        $this->setupTranslations();
    }

    /**
     * Load translations
     *
     * @access public
     */
    public function setupTranslations()
    {
        Translator::load($this->getCurrentLanguage());
    }

    /**
     * Get current timezone
     *
     * @access public
     * @return string
     */
    public function getCurrentTimezone()
    {
        if ($this->userSession->isLogged() && ! empty($this->session['user']['timezone'])) {
            return $this->session['user']['timezone'];
        }

        return $this->get('application_timezone', 'UTC');
    }

    /**
     * Set timezone
     *
     * @access public
     */
    public function setupTimezone()
    {
        date_default_timezone_set($this->getCurrentTimezone());
    }

    /**
     * Optimize the Sqlite database
     *
     * @access public
     * @return boolean
     */
    public function optimizeDatabase()
    {
        return $this->db->getconnection()->exec("VACUUM");
    }

    /**
     * Compress the Sqlite database
     *
     * @access public
     * @return string
     */
    public function downloadDatabase()
    {
        return gzencode(file_get_contents(DB_FILENAME));
    }

    /**
     * Get the Sqlite database size in bytes
     *
     * @access public
     * @return integer
     */
    public function getDatabaseSize()
    {
        return DB_DRIVER === 'sqlite' ? filesize(DB_FILENAME) : 0;
    }

    /**
     * Regenerate a token
     *
     * @access public
     * @param  string   $option   Parameter name
     */
    public function regenerateToken($option)
    {
        $this->save(array($option => Token::getToken()));
    }

    /**
     * Prepare data before save
     *
     * @access public
     * @return array
     */
    public function prepare(array $values)
    {
        if (! empty($values['application_url']) && substr($values['application_url'], -1) !== '/') {
            $values['application_url'] = $values['application_url'].'/';
        }

        return $values;
    }
}
