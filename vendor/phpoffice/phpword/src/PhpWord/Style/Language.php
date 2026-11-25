<?php

/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @see         https://github.com/PHPOffice/PHPWord
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Style;

use InvalidArgumentException;

/**
 * Language
 * A couple of predefined values are defined here, see the websites below for more values.
 *
 * @see http://www.datypic.com/sc/ooxml/t-w_CT_Language.html
 * @see https://technet.microsoft.com/en-us/library/cc287874(v=office.12).aspx
 */
final class Language extends AbstractStyle
{
    const EN_US = 'en-US';
    const EN_US_ID = 1033;

    const EN_GB = 'en-GB';
    const EN_GB_ID = 2057;

    const FR_FR = 'fr-FR';
    const FR_FR_ID = 1036;

    const FR_BE = 'fr-BE';
    const FR_BE_ID = 2060;

    const FR_CH = 'fr-CH';
    const FR_CH_ID = 4108;

    const ES_ES = 'es-ES';
    const ES_ES_ID = 3082;

    const DE_DE = 'de-DE';
    const DE_DE_ID = 1031;

    const DE_CH = 'de-CH';
    const DE_CH_ID = 2055;

    const HE_IL = 'he-IL';
    const HE_IL_ID = 1037;

    const IT_IT = 'it-IT';
    const IT_IT_ID = 1040;

    const IT_CH = 'it-CH';
    const IT_CH_ID = 2064;

    const JA_JP = 'ja-JP';
    const JA_JP_ID = 1041;

    const KO_KR = 'ko-KR';
    const KO_KR_ID = 1042;

    const ZH_CN = 'zh-CN';
    const ZH_CN_ID = 2052;

    const HI_IN = 'hi-IN';
    const HI_IN_ID = 1081;

    const PT_BR = 'pt-BR';
    const PT_BR_ID = 1046;

    const NL_NL = 'nl-NL';
    const NL_NL_ID = 1043;

    const SV_SE = 'sv-SE';
    const SV_SE_ID = 1053;

    const UK_UA = 'uk-UA';
    const UK_UA_ID = 1058;

    const RU_RU = 'ru-RU';
    const RU_RU_ID = 1049;

    /**
     * Language ID, used for RTF document generation.
     *
     * @var int
     *
     * @see https://technet.microsoft.com/en-us/library/cc179219.aspx
     */
    private $langId;

    /**
     * Latin Language.
     *
     * @var string
     */
    private $latin;

    /**
     * East Asian Language.
     *
     * @var string
     */
    private $eastAsia;

    /**
     * Complex Script Language.
     *
     * @var string
     */
    private $bidirectional;

    /**
     * Constructor.
     *
     * @param null|string $latin
     * @param null|string $eastAsia
     * @param null|string $bidirectional
     */
    public function __construct($latin = null, $eastAsia = null, $bidirectional = null)
    {
        if (!empty($latin)) {
            $this->setLatin($latin);
        }
        if (!empty($eastAsia)) {
            $this->setEastAsia($eastAsia);
        }
        if (!empty($bidirectional)) {
            $this->setBidirectional($bidirectional);
        }
    }

    /**
     * Set the Latin Language.
     *
     * @param string $latin
     *            The value for the latin language
     *
     * @return self
     */
    public function setLatin($latin)
    {
        $this->latin = $this->validateLocale($latin);

        return $this;
    }

    /**
     * Get the Latin Language.
     *
     * @return null|string
     */
    public function getLatin()
    {
        return $this->latin;
    }

    /**
     * Set the Language ID.
     *
     * @param int $langId
     *            The value for the language ID
     *
     * @return self
     *
     * @see https://technet.microsoft.com/en-us/library/cc287874(v=office.12).aspx
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;

        return $this;
    }

    /**
     * Get the Language ID.
     *
     * @return int
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     * Set the East Asian Language.
     *
     * @param string $eastAsia
     *            The value for the east asian language
     *
     * @return self
     */
    public function setEastAsia($eastAsia)
    {
        $this->eastAsia = $this->validateLocale($eastAsia);

        return $this;
    }

    /**
     * Get the East Asian Language.
     *
     * @return null|string
     */
    public function getEastAsia()
    {
        return $this->eastAsia;
    }

    /**
     * Set the Complex Script Language.
     *
     * @param string $bidirectional
     *            The value for the complex script language
     *
     * @return self
     */
    public function setBidirectional($bidirectional)
    {
        $this->bidirectional = $this->validateLocale($bidirectional);

        return $this;
    }

    /**
     * Get the Complex Script Language.
     *
     * @return null|string
     */
    public function getBidirectional()
    {
        return $this->bidirectional;
    }

    /**
     * Validates that the language passed is in the format xx-xx.
     *
     * @param string $locale
     *
     * @return string
     */
    private function validateLocale($locale)
    {
        if ($locale !== null) {
            $locale = str_replace('_', '-', $locale);
        }

        if ($locale !== null && strlen($locale) === 2) {
            return strtolower($locale) . '-' . strtoupper($locale);
        }
        if ($locale === 'und') {
            return 'en-EN';
        }
        if ($locale !== null && $locale !== 'zxx' && strstr($locale, '-') === false) {
            throw new InvalidArgumentException($locale . ' is not a valid language code');
        }

        return $locale;
    }
}
