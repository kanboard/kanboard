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
            'bg_BG',
            'bs_BA',
            'ca_ES',
            'cs_CZ',
            'da_DK',
            'de_DE',
            'de_DE_du',
            'en_GB',
            'en_US',
            'es_ES',
            'es_VE',
            'fr_FR',
            'el_GR',
            'it_IT',
            'hr_HR',
            'hu_HU',
            'mk_MK',
            'my_MY',
            'nl_NL',
            'nb_NO',
            'pl_PL',
            'pt_PT',
            'pt_BR',
            'ro_RO',
            'ru_RU',
            'sr_Latn_RS',
            'fi_FI',
            'sk_SK',
            'sv_SE',
            'tr_TR',
            'uk_UA',
            'ko_KR',
            'zh_CN',
            'zh_TW',
            'ja_JP',
            'th_TH',
            'vi_VN',
            'fa_IR',
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
            'bg_BG' => 'Български',
            'bs_BA' => 'Bosanski',
            'ca_ES' => 'Català',
            'cs_CZ' => 'Čeština',
            'da_DK' => 'Dansk',
            'de_DE' => 'Deutsch (Sie)',
            'de_DE_du' => 'Deutsch (du)',
            'en_GB' => 'English (GB)',
            'en_US' => 'English (US)',
            'es_ES' => 'Español (España)',
            'es_VE' => 'Español (Venezuela)',
            'fr_FR' => 'Français',
            'el_GR' => 'Greek (Ελληνικά)',
            'hr_HR' => 'Hrvatski',
            'it_IT' => 'Italiano',
            'hu_HU' => 'Magyar',
            'mk_MK' => 'Македонски',
            'my_MY' => 'Melayu',
            'nl_NL' => 'Nederlands',
            'nb_NO' => 'Norsk',
            'pl_PL' => 'Polski',
            'pt_PT' => 'Português',
            'pt_BR' => 'Português (Brasil)',
            'ro_RO' => 'Română',
            'ru_RU' => 'Русский',
            'sr_Latn_RS' => 'Srpski',
            'fi_FI' => 'Suomi',
            'sk_SK' => 'Slovenčina',
            'sv_SE' => 'Svenska',
            'tr_TR' => 'Türkçe',
            'uk_UA' => 'Українська',
            'ko_KR' => '한국어',
            'zh_CN' => '中文(简体)',
            'zh_TW' => '中文(繁體)',
            'ja_JP' => '日本語',
            'th_TH' => 'ไทย',
            'vi_VN' => 'Tiếng Việt',
            'fa_IR' => 'فارسی',
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
            'bg_BG' => 'bg',
            'cs_CZ' => 'cs',
            'ca_ES' => 'ca',
            'da_DK' => 'da',
            'de_DE' => 'de',
            'de_DE_du' => 'de',
            'en_GB' => 'en-GB',
            'en_US' => 'en',
            'es_ES' => 'es',
            'es_VE' => 'es',
            'fr_FR' => 'fr',
            'it_IT' => 'it',
            'hr_HR' => 'hr',
            'hu_HU' => 'hu',
            'nl_NL' => 'nl',
            'nb_NO' => 'no',
            'pl_PL' => 'pl',
            'pt_PT' => 'pt',
            'pt_BR' => 'pt-BR',
            'ro_RO' => 'ro',
            'ru_RU' => 'ru',
            'sr_Latn_RS' => 'sr',
            'fi_FI' => 'fi',
            'sk_SK' => 'sk',
            'sv_SE' => 'sv',
            'tr_TR' => 'tr',
            'uk_UA' => 'uk',
            'ko_KR' => 'ko',
            'zh_CN' => 'zh-CN',
            'zh_TW' => 'zh-TW',
            'ja_JP' => 'ja',
            'th_TH' => 'th',
            'id_ID' => 'id',
            'el_GR' => 'el',
            'fa_IR' => 'fa',
            'vi_VN' => 'vi',
            'bs_BA' => 'bs',
            'mk_MK' => 'mk',
            'my_MY' => 'my',
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
        return $this->userSession->getLanguage() ?: $this->configModel->get('application_language', 'en_US');
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
