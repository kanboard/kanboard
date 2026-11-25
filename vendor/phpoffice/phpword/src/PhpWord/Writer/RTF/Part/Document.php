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

namespace PhpOffice\PhpWord\Writer\RTF\Part;

use PhpOffice\PhpWord\Element\Footer;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Writer\RTF\Element\Container;
use PhpOffice\PhpWord\Writer\RTF\Style\Section as SectionStyleWriter;

/**
 * RTF document part writer.
 *
 * @since 0.11.0
 * @see  http://www.biblioscape.com/rtf15_spec.htm#Heading24
 */
class Document extends AbstractPart
{
    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $content = '';

        $content .= $this->writeInfo();
        $content .= $this->writeFormatting();
        $content .= $this->writeSections();

        return $content;
    }

    /**
     * Write document information.
     *
     * @return string
     */
    private function writeInfo()
    {
        $docProps = $this->getParentWriter()->getPhpWord()->getDocInfo();
        $properties = ['title', 'subject', 'category', 'keywords', 'comment',
            'author', 'operator', 'creatim', 'revtim', 'company', 'manager', ];
        $mapping = [
            'comment' => 'description',
            'author' => 'creator',
            'operator' => 'lastModifiedBy',
            'creatim' => 'created',
            'revtim' => 'modified', ];
        $dateFields = ['creatim', 'revtim'];

        $content = '';

        $content .= '{';
        $content .= '\info';
        foreach ($properties as $property) {
            $method = 'get' . ($mapping[$property] ?? $property);
            if (!in_array($property, $dateFields) && Settings::isOutputEscapingEnabled()) {
                $value = $this->escaper->escape($docProps->$method());
            } else {
                $value = $docProps->$method();
            }
            $value = in_array($property, $dateFields) ? $this->getDateValue($value) : $value;
            $content .= "{\\{$property} {$value}}";
        }
        $content .= '}';
        $content .= PHP_EOL;

        return $content;
    }

    /**
     * Write document formatting properties.
     *
     * @return string
     */
    private function writeFormatting()
    {
        $docSettings = $this->getParentWriter()->getPhpWord()->getSettings();
        // Applies a language to a text run (defaults to 1036 : French (France))
        $langId = $docSettings->getThemeFontLang() != null && $docSettings->getThemeFontLang()->getLangId() != null ? $docSettings->getThemeFontLang()->getLangId() : 1036;

        $content = '';

        $content .= '\deftab720'; // Set the default tab size (720 twips)
        $content .= '\viewkind1'; // Set the view mode of the document

        $content .= '\uc1'; // Set the numberof bytes that follows a unicode character
        $content .= '\pard'; // Resets to default paragraph properties.
        $content .= '\nowidctlpar'; // No widow/orphan control
        $content .= '\lang' . $langId;
        $content .= '\kerning1'; // Point size (in half-points) above which to kern character pairs
        $content .= '\fs' . (Settings::getDefaultFontSize() * 2); // Set the font size in half-points
        if ($docSettings->hasEvenAndOddHeaders()) {
            $content .= '\\facingp';
        }
        $content .= PHP_EOL;

        return $content;
    }

    /**
     * Write titlepg directive if any "f" headers or footers.
     *
     * @param \PhpOffice\PhpWord\Element\Section $section
     *
     * @return string
     */
    private static function writeTitlepg($section)
    {
        foreach ($section->getHeaders() as $header) {
            if ($header->getType() === Footer::FIRST) {
                return '\\titlepg' . PHP_EOL;
            }
        }
        foreach ($section->getFooters() as $header) {
            if ($header->getType() === Footer::FIRST) {
                return '\\titlepg' . PHP_EOL;
            }
        }

        return '';
    }

    /**
     * Write sections.
     *
     * @return string
     */
    private function writeSections()
    {
        $content = '';

        $sections = $this->getParentWriter()->getPhpWord()->getSections();
        $evenOdd = $this->getParentWriter()->getPhpWord()->getSettings()->hasEvenAndOddHeaders();
        $sectOwed = false;
        foreach ($sections as $section) {
            if ($sectOwed) {
                $content .= '\sect' . PHP_EOL;
            } else {
                $sectOwed = true;
            }
            $styleWriter = new SectionStyleWriter($section->getStyle());
            $styleWriter->setParentWriter($this->getParentWriter());
            $content .= $styleWriter->write();
            $content .= self::writeTitlepg($section);

            foreach ($section->getHeaders() as $header) {
                $type = $header->getType();
                if ($evenOdd || $type !== Footer::EVEN) {
                    $content .= '{\\header';
                    if ($type === Footer::FIRST) {
                        $content .= 'f';
                    } elseif ($evenOdd) {
                        $content .= ($type === Footer::EVEN) ? 'l' : 'r';
                    }
                    foreach ($header->getElements() as $element) {
                        $cl = get_class($element);
                        $cl2 = str_replace('Element', 'Writer\\RTF\\Element', $cl);
                        if (class_exists($cl2)) {
                            $elementWriter = new $cl2($this->getParentWriter(), $element);
                            $content .= $elementWriter->write();
                        }
                    }
                    $content .= '}' . PHP_EOL;
                }
            }
            foreach ($section->getFooters() as $footer) {
                $type = $footer->getType();
                if ($evenOdd || $type !== Footer::EVEN) {
                    $content .= '{\\footer';
                    if ($type === Footer::FIRST) {
                        $content .= 'f';
                    } elseif ($evenOdd) {
                        $content .= ($type === Footer::EVEN) ? 'l' : 'r';
                    }
                    foreach ($footer->getElements() as $element) {
                        $cl = get_class($element);
                        $cl2 = str_replace('Element', 'Writer\\RTF\\Element', $cl);
                        if (class_exists($cl2)) {
                            $elementWriter = new $cl2($this->getParentWriter(), $element);
                            $content .= $elementWriter->write();
                        }
                    }
                    $content .= '}' . PHP_EOL;
                }
            }

            $elementWriter = new Container($this->getParentWriter(), $section);
            $content .= $elementWriter->write();
        }

        return $content;
    }

    /**
     * Get date value.
     *
     * The format of date value is `\yr?\mo?\dy?\hr?\min?\sec?`
     *
     * @param int $value
     *
     * @return string
     */
    private function getDateValue($value)
    {
        $dateParts = [
            'Y' => 'yr',
            'm' => 'mo',
            'd' => 'dy',
            'H' => 'hr',
            'i' => 'min',
            's' => 'sec',
        ];
        $result = '';
        foreach ($dateParts as $dateFormat => $controlWord) {
            $result .= '\\' . $controlWord . date($dateFormat, $value);
        }

        return $result;
    }
}
