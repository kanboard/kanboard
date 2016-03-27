<?php

namespace Kanboard\Model;

use Kanboard\Core\Translator;
use Kanboard\Core\Security\Token;

/**
 * Config model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Config extends Setting
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
            'bs_BA' => 'Bosanski',
            'cs_CZ' => 'Čeština',
            'da_DK' => 'Dansk',
            'de_DE' => 'Deutsch',
            'en_US' => 'English',
            'es_ES' => 'Español',
            'fr_FR' => 'Français',
            'el_GR' => 'Grec',
            'it_IT' => 'Italiano',
            'hu_HU' => 'Magyar',
            'my_MY' => 'Melayu',
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
            'ko_KR' => '한국어',
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
            'cs_CZ' => 'cs',
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
            'ko_KR' => 'ko',
            'zh_CN' => 'zh-cn',
            'ja_JP' => 'ja',
            'th_TH' => 'th',
            'id_ID' => 'id',
            'el_GR' => 'el',
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
        if ($this->userSession->isLogged() && ! empty($this->sessionStorage->user['language'])) {
            return $this->sessionStorage->user['language'];
        }

        return $this->get('application_language', 'en_US');
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
     * Get a config variable from the session or the database
     *
     * @access public
     * @param  string   $name            Parameter name
     * @param  string   $default_value   Default value of the parameter
     * @return string
     */
    public function get($name, $default_value = '')
    {
        $options = $this->memoryCache->proxy($this, 'getAll');
        return isset($options[$name]) && $options[$name] !== '' ? $options[$name] : $default_value;
    }

    /**
     * Reload settings in the session and the translations
     *
     * @access public
     */
    public function reload()
    {
        $this->setupTranslations();
    }

    /**
     * Optimize the Sqlite database
     *
     * @access public
     * @return boolean
     */
    public function optimizeDatabase()
    {
        return $this->db->getconnection()->exec('VACUUM');
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
     * @return boolean
     */
    public function regenerateToken($option)
    {
        return $this->save(array($option => Token::getToken()));
    }

    /**
     * Prepare data before save
     *
     * @access public
     * @param  array $values
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
