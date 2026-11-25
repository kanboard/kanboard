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

namespace PhpOffice\PhpWord\Writer\Word2007\Part;

use PhpOffice\PhpWord\ComplexType\ProofState;
use PhpOffice\PhpWord\ComplexType\TrackChangesView;
use PhpOffice\PhpWord\Shared\Microsoft\PasswordEncoder;
use PhpOffice\PhpWord\Style\Language;

/**
 * Word2007 settings part writer: word/settings.xml.
 *
 * @see  http://www.schemacentral.com/sc/ooxml/t-w_CT_Settings.html
 */
class Settings extends AbstractPart
{
    /**
     * Settings value.
     *
     * @var array
     */
    private $settings = [];

    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $this->getSettings();

        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
        $xmlWriter->startElement('w:settings');
        $xmlWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xmlWriter->writeAttribute('xmlns:w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xmlWriter->writeAttribute('xmlns:m', 'http://schemas.openxmlformats.org/officeDocument/2006/math');
        $xmlWriter->writeAttribute('xmlns:sl', 'http://schemas.openxmlformats.org/schemaLibrary/2006/main');
        $xmlWriter->writeAttribute('xmlns:o', 'urn:schemas-microsoft-com:office:office');
        $xmlWriter->writeAttribute('xmlns:v', 'urn:schemas-microsoft-com:vml');
        $xmlWriter->writeAttribute('xmlns:w10', 'urn:schemas-microsoft-com:office:word');

        foreach ($this->settings as $settingKey => $settingValue) {
            $this->writeSetting($xmlWriter, $settingKey, $settingValue);
        }

        $xmlWriter->endElement(); // w:settings

        return $xmlWriter->getData();
    }

    /**
     * Write indivual setting, recursive to any child settings.
     *
     * @param \PhpOffice\PhpWord\Shared\XMLWriter $xmlWriter
     * @param string $settingKey
     * @param array|string $settingValue
     */
    protected function writeSetting($xmlWriter, $settingKey, $settingValue): void
    {
        if ($settingValue == '') {
            $xmlWriter->writeElement($settingKey);
        } elseif (is_array($settingValue) && !empty($settingValue)) {
            $xmlWriter->startElement($settingKey);

            /** @var array $settingValue Type hint */
            foreach ($settingValue as $childKey => $childValue) {
                if ($childKey == '@attributes') {
                    foreach ($childValue as $key => $val) {
                        $xmlWriter->writeAttribute($key, $val);
                    }
                } else {
                    $this->writeSetting($xmlWriter, $childKey, $childValue);
                }
            }
            $xmlWriter->endElement();
        }
    }

    /**
     * Get settings.
     */
    private function getSettings(): void
    {
        /** @var \PhpOffice\PhpWord\Metadata\Settings $documentSettings */
        $documentSettings = $this->getParentWriter()->getPhpWord()->getSettings();

        // Default settings
        $this->settings = [
            'w:defaultTabStop' => ['@attributes' => ['w:val' => '708']],
            'w:hyphenationZone' => ['@attributes' => ['w:val' => '425']],
            'w:characterSpacingControl' => ['@attributes' => ['w:val' => 'doNotCompress']],
            'w:decimalSymbol' => ['@attributes' => ['w:val' => $documentSettings->getDecimalSymbol()]],
            'w:listSeparator' => ['@attributes' => ['w:val' => ';']],
            'w:compat' => [],
            'm:mathPr' => [
                'm:mathFont' => ['@attributes' => ['m:val' => 'Cambria Math']],
                'm:brkBin' => ['@attributes' => ['m:val' => 'before']],
                'm:brkBinSub' => ['@attributes' => ['m:val' => '--']],
                'm:smallFrac' => ['@attributes' => ['m:val' => 'off']],
                'm:dispDef' => '',
                'm:lMargin' => ['@attributes' => ['m:val' => '0']],
                'm:rMargin' => ['@attributes' => ['m:val' => '0']],
                'm:defJc' => ['@attributes' => ['m:val' => 'centerGroup']],
                'm:wrapIndent' => ['@attributes' => ['m:val' => '1440']],
                'm:intLim' => ['@attributes' => ['m:val' => 'subSup']],
                'm:naryLim' => ['@attributes' => ['m:val' => 'undOvr']],
            ],
            'w:clrSchemeMapping' => [
                '@attributes' => [
                    'w:bg1' => 'light1',
                    'w:t1' => 'dark1',
                    'w:bg2' => 'light2',
                    'w:t2' => 'dark2',
                    'w:accent1' => 'accent1',
                    'w:accent2' => 'accent2',
                    'w:accent3' => 'accent3',
                    'w:accent4' => 'accent4',
                    'w:accent5' => 'accent5',
                    'w:accent6' => 'accent6',
                    'w:hyperlink' => 'hyperlink',
                    'w:followedHyperlink' => 'followedHyperlink',
                ],
            ],
        ];

        $this->setOnOffValue('w:mirrorMargins', $documentSettings->hasMirrorMargins());
        $this->setOnOffValue('w:hideSpellingErrors', $documentSettings->hasHideSpellingErrors());
        $this->setOnOffValue('w:hideGrammaticalErrors', $documentSettings->hasHideGrammaticalErrors());
        $this->setOnOffValue('w:trackRevisions', $documentSettings->hasTrackRevisions());
        $this->setOnOffValue('w:doNotTrackMoves', $documentSettings->hasDoNotTrackMoves());
        $this->setOnOffValue('w:doNotTrackFormatting', $documentSettings->hasDoNotTrackFormatting());
        $this->setOnOffValue('w:evenAndOddHeaders', $documentSettings->hasEvenAndOddHeaders());
        $this->setOnOffValue('w:updateFields', $documentSettings->hasUpdateFields());
        $this->setOnOffValue('w:autoHyphenation', $documentSettings->hasAutoHyphenation());
        $this->setOnOffValue('w:doNotHyphenateCaps', $documentSettings->hasDoNotHyphenateCaps());
        $this->setOnOffValue('w:bookFoldPrinting', $documentSettings->hasBookFoldPrinting());

        $this->setThemeFontLang($documentSettings->getThemeFontLang());
        $this->setRevisionView($documentSettings->getRevisionView());
        $this->setDocumentProtection($documentSettings->getDocumentProtection());
        $this->setProofState($documentSettings->getProofState());
        $this->setZoom($documentSettings->getZoom());
        $this->setConsecutiveHyphenLimit($documentSettings->getConsecutiveHyphenLimit());
        $this->setHyphenationZone($documentSettings->getHyphenationZone());
        $this->setCompatibility();
    }

    /**
     * Adds a boolean attribute to the settings array.
     *
     * @param string $settingName
     * @param null|bool $booleanValue
     */
    private function setOnOffValue($settingName, $booleanValue): void
    {
        if (!is_bool($booleanValue)) {
            return;
        }

        $value = $booleanValue ? 'true' : 'false';
        $this->settings[$settingName] = ['@attributes' => ['w:val' => $value]];
    }

    /**
     * Get protection settings.
     *
     * @param \PhpOffice\PhpWord\Metadata\Protection $documentProtection
     */
    private function setDocumentProtection($documentProtection): void
    {
        if ($documentProtection->getEditing() !== null) {
            if ($documentProtection->getPassword() == null) {
                $this->settings['w:documentProtection'] = [
                    '@attributes' => [
                        'w:enforcement' => 1,
                        'w:edit' => $documentProtection->getEditing(),
                    ],
                ];
            } else {
                if ($documentProtection->getSalt() == null) {
                    $documentProtection->setSalt((string) openssl_random_pseudo_bytes(16));
                }
                $passwordHash = PasswordEncoder::hashPassword($documentProtection->getPassword(), $documentProtection->getAlgorithm(), $documentProtection->getSalt(), $documentProtection->getSpinCount());
                $this->settings['w:documentProtection'] = [
                    '@attributes' => [
                        'w:enforcement' => 1,
                        'w:edit' => $documentProtection->getEditing(),
                        'w:cryptProviderType' => 'rsaFull',
                        'w:cryptAlgorithmClass' => 'hash',
                        'w:cryptAlgorithmType' => 'typeAny',
                        'w:cryptAlgorithmSid' => PasswordEncoder::getAlgorithmId($documentProtection->getAlgorithm()),
                        'w:cryptSpinCount' => $documentProtection->getSpinCount(),
                        'w:hash' => $passwordHash,
                        'w:salt' => base64_encode($documentProtection->getSalt()),
                    ],
                ];
            }
        }
    }

    /**
     * Set the Proof state.
     */
    private function setProofState(?ProofState $proofState = null): void
    {
        if ($proofState != null && $proofState->getGrammar() !== null && $proofState->getSpelling() !== null) {
            $this->settings['w:proofState'] = [
                '@attributes' => [
                    'w:spelling' => $proofState->getSpelling(),
                    'w:grammar' => $proofState->getGrammar(),
                ],
            ];
        }
    }

    /**
     * Set the Revision View.
     */
    private function setRevisionView(?TrackChangesView $trackChangesView = null): void
    {
        if ($trackChangesView != null) {
            $revisionView = [];
            $revisionView['w:markup'] = $trackChangesView->hasMarkup() ? 'true' : 'false';
            $revisionView['w:comments'] = $trackChangesView->hasComments() ? 'true' : 'false';
            $revisionView['w:insDel'] = $trackChangesView->hasInsDel() ? 'true' : 'false';
            $revisionView['w:formatting'] = $trackChangesView->hasFormatting() ? 'true' : 'false';
            $revisionView['w:inkAnnotations'] = $trackChangesView->hasInkAnnotations() ? 'true' : 'false';

            $this->settings['w:revisionView'] = ['@attributes' => $revisionView];
        }
    }

    /**
     * Sets the language.
     */
    private function setThemeFontLang(?Language $language = null): void
    {
        $latinLanguage = ($language == null || $language->getLatin() === null) ? 'en-US' : $language->getLatin();
        $lang = [];
        $lang['w:val'] = $latinLanguage;
        if ($language != null) {
            $lang['w:eastAsia'] = $language->getEastAsia() === null ? 'x-none' : $language->getEastAsia();
            $lang['w:bidi'] = $language->getBidirectional() === null ? 'x-none' : $language->getBidirectional();
        }
        $this->settings['w:themeFontLang'] = ['@attributes' => $lang];
    }

    /**
     * Set the magnification.
     *
     * @param mixed $zoom
     */
    private function setZoom($zoom = null): void
    {
        if ($zoom !== null) {
            $attr = is_int($zoom) ? 'w:percent' : 'w:val';
            $this->settings['w:zoom'] = ['@attributes' => [$attr => $zoom]];
        }
    }

    /**
     * @param null|int $consecutiveHyphenLimit
     */
    private function setConsecutiveHyphenLimit($consecutiveHyphenLimit): void
    {
        if ($consecutiveHyphenLimit === null) {
            return;
        }

        $this->settings['w:consecutiveHyphenLimit'] = [
            '@attributes' => ['w:val' => $consecutiveHyphenLimit],
        ];
    }

    /**
     * @param null|float $hyphenationZone
     */
    private function setHyphenationZone($hyphenationZone): void
    {
        if ($hyphenationZone === null) {
            return;
        }

        $this->settings['w:hyphenationZone'] = [
            '@attributes' => ['w:val' => $hyphenationZone],
        ];
    }

    /**
     * Get compatibility setting.
     */
    private function setCompatibility(): void
    {
        $compatibility = $this->getParentWriter()->getPhpWord()->getCompatibility();
        if ($compatibility->getOoxmlVersion() !== null) {
            $this->settings['w:compat']['w:compatSetting'] = [
                '@attributes' => [
                    'w:name' => 'compatibilityMode',
                    'w:uri' => 'http://schemas.microsoft.com/office/word',
                    'w:val' => $compatibility->getOoxmlVersion(),
                ],
            ];
        }
    }
}
