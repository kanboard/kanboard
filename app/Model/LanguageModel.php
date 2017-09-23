<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use Kanboard\Core\Translator;

/**
 * Class Language
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class LanguageModel extends Base
{
    /**
     * Get all language codes
     *
     * @static
     * @access public
     * @return string[]
     */
    public static function getCodes()
    {
        return array(
            'id_ID',
            'bs_BA',
			'ca_ES',
            'cs_CZ',
            'da_DK',
            'de_DE',
            'en_US',
            'es_ES',
            'fr_FR',
            'el_GR',
            'it_IT',
            'hr_HR',
            'hu_HU',
            'my_MY',
            'nl_NL',
            'nb_NO',
            'pl_PL',
            'pt_PT',
            'pt_BR',
            'ru_RU',
            'sr_Latn_RS',
            'fi_FI',
            'sv_SE',
            'tr_TR',
            'ko_KR',
            'zh_CN',
            'ja_JP',
            'th_TH',
            'vi_VN',
        );
    }

    /**
     * Find language code
     *
     * @static
     * @access public
     * @param  string $code
     * @return string
     */
    public static function findCode($code)
    {
        $code = str_replace('-', '_', $code);
        return in_array($code, self::getCodes()) ? $code : '';
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
			'ca_ES' => 'Català',
            'cs_CZ' => 'Čeština',
            'da_DK' => 'Dansk',
            'de_DE' => 'Deutsch',
            'en_US' => 'English',
            'es_ES' => 'Español',
            'fr_FR' => 'Français',
            'el_GR' => 'Grec',
            'hr_HR' => 'Hrvatski',
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
            'vi_VN' => 'Tiếng Việt',
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
			'ca_ES' => 'ca',
            'da_DK' => 'da',
            'de_DE' => 'de',
            'en_US' => 'en',
            'es_ES' => 'es',
            'fr_FR' => 'fr',
            'it_IT' => 'it',
            'hr_HR' => 'hr',
            'hu_HU' => 'hu',
            'nl_NL' => 'nl',
            'nb_NO' => 'nb',
            'pl_PL' => 'pl',
            'pt_PT' => 'pt',
            'pt_BR' => 'pt-BR',
            'ru_RU' => 'ru',
            'sr_Latn_RS' => 'sr',
            'fi_FI' => 'fi',
            'sv_SE' => 'sv',
            'tr_TR' => 'tr',
            'ko_KR' => 'ko',
            'zh_CN' => 'zh-CN',
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

        return $this->configModel->get('application_language', 'en_US');
    }

    /**
     * Load translations for the current language
     *
     * @access public
     */
    public function loadCurrentLanguage()
    {
        Translator::load($this->getCurrentLanguage());
    }
}
