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

namespace PhpOffice\PhpWord\Element;

use InvalidArgumentException;
use PhpOffice\PhpWord\Style\Font;

/**
 * Field element.
 *
 * @since 0.11.0
 * @see  http://www.schemacentral.com/sc/ooxml/t-w_CT_SimpleField.html
 */
class Field extends AbstractElement
{
    /**
     * Field properties and options. Depending on type, a field can have different properties
     * and options.
     *
     * @var array
     */
    protected $fieldsArray = [
        'PAGE' => [
            'properties' => [
                'format' => ['Arabic', 'ArabicDash', 'alphabetic', 'ALPHABETIC', 'roman', 'ROMAN'],
            ],
            'options' => ['PreserveFormat'],
        ],
        'NUMPAGES' => [
            'properties' => [
                'format' => ['Arabic', 'ArabicDash', 'CardText', 'DollarText', 'Ordinal', 'OrdText',
                    'alphabetic', 'ALPHABETIC', 'roman', 'ROMAN', 'Caps', 'FirstCap', 'Lower', 'Upper', ],
                'numformat' => ['0', '0,00', '#.##0', '#.##0,00', '€ #.##0,00(€ #.##0,00)', '0%', '0,00%'],
            ],
            'options' => ['PreserveFormat'],
        ],
        'DATE' => [
            'properties' => [
                'dateformat' => [
                    // Generic formats
                    'yyyy-MM-dd', 'yyyy-MM', 'MMM-yy', 'MMM-yyyy', 'h:mm am/pm', 'h:mm:ss am/pm', 'HH:mm', 'HH:mm:ss',
                    // Day-Month-Year formats
                    'dddd d MMMM yyyy', 'd MMMM yyyy', 'd-MMM-yy', 'd MMM. yy',
                    'd-M-yy', 'd-M-yy h:mm', 'd-M-yy h:mm:ss', 'd-M-yy h:mm am/pm', 'd-M-yy h:mm:ss am/pm', 'd-M-yy HH:mm', 'd-M-yy HH:mm:ss',
                    'd/M/yy', 'd/M/yy h:mm', 'd/M/yy h:mm:ss', 'd/M/yy h:mm am/pm', 'd/M/yy h:mm:ss am/pm', 'd/M/yy HH:mm', 'd/M/yy HH:mm:ss',
                    'd-M-yyyy', 'd-M-yyyy h:mm', 'd-M-yyyy h:mm:ss', 'd-M-yyyy h:mm am/pm', 'd-M-yyyy h:mm:ss am/pm', 'd-M-yyyy HH:mm', 'd-M-yyyy HH:mm:ss',
                    'd/M/yyyy', 'd/M/yyyy h:mm', 'd/M/yyyy h:mm:ss', 'd/M/yyyy h:mm am/pm', 'd/M/yyyy h:mm:ss am/pm', 'd/M/yyyy HH:mm', 'd/M/yyyy HH:mm:ss',
                    // Month-Day-Year formats
                    'dddd, MMMM d yyyy', 'MMMM d yyyy', 'MMM-d-yy', 'MMM. d yy',
                    'M-d-yy', 'M-d-yy h:mm', 'M-d-yy h:mm:ss', 'M-d-yy h:mm am/pm', 'M-d-yy h:mm:ss am/pm', 'M-d-yy HH:mm', 'M-d-yy HH:mm:ss',
                    'M/d/yy', 'M/d/yy h:mm', 'M/d/yy h:mm:ss', 'M/d/yy h:mm am/pm', 'M/d/yy h:mm:ss am/pm', 'M/d/yy HH:mm', 'M/d/yy HH:mm:ss',
                    'M-d-yyyy', 'M-d-yyyy h:mm', 'M-d-yyyy h:mm:ss', 'M-d-yyyy h:mm am/pm', 'M-d-yyyy h:mm:ss am/pm', 'M-d-yyyy HH:mm', 'M-d-yyyy HH:mm:ss',
                    'M/d/yyyy', 'M/d/yyyy h:mm', 'M/d/yyyy h:mm:ss', 'M/d/yyyy h:mm am/pm', 'M/d/yyyy h:mm:ss am/pm', 'M/d/yyyy HH:mm', 'M/d/yyyy HH:mm:ss',
                ],
            ],
            'options' => ['PreserveFormat', 'LunarCalendar', 'SakaEraCalendar', 'LastUsedFormat'],
        ],
        'MACROBUTTON' => [
            'properties' => ['macroname' => ''],
        ],
        'XE' => [
            'properties' => [],
            'options' => ['Bold', 'Italic'],
        ],
        'INDEX' => [
            'properties' => [],
            'options' => ['PreserveFormat'],
        ],
        'STYLEREF' => [
            'properties' => ['StyleIdentifier' => ''],
            'options' => ['PreserveFormat'],
        ],
        'FILENAME' => [
            'properties' => [
                'format' => ['Upper', 'Lower', 'FirstCap', 'Caps'],
            ],
            'options' => ['Path', 'PreserveFormat'],
        ],
        'REF' => [
            'properties' => ['name' => ''],
            'options' => ['f', 'h', 'n', 'p', 'r', 't', 'w'],
        ],
    ];

    /**
     * Field type.
     *
     * @var string
     */
    protected $type;

    /**
     * Field text.
     *
     * @var string|TextRun
     */
    protected $text;

    /**
     * Field properties.
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Field options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Font style.
     *
     * @var Font|string
     */
    protected $fontStyle;

    /**
     * Set Font style.
     *
     * @param array|Font|string $style
     *
     * @return Font|string
     */
    public function setFontStyle($style = null)
    {
        if ($style instanceof Font) {
            $this->fontStyle = $style;
        } elseif (is_array($style)) {
            $this->fontStyle = new Font('text');
            $this->fontStyle->setStyleByArray($style);
        } elseif (null === $style) {
            $this->fontStyle = null;
        } else {
            $this->fontStyle = $style;
        }

        return $this->fontStyle;
    }

    /**
     * Get Font style.
     *
     * @return Font|string
     */
    public function getFontStyle()
    {
        return $this->fontStyle;
    }

    /**
     * Create a new Field Element.
     *
     * @param string $type
     * @param array $properties
     * @param array $options
     * @param null|string|TextRun $text
     * @param array|Font|string $fontStyle
     */
    public function __construct($type = null, $properties = [], $options = [], $text = null, $fontStyle = null)
    {
        $this->setType($type);
        $this->setProperties($properties);
        $this->setOptions($options);
        $this->setText($text);
        $this->setFontStyle($fontStyle);
    }

    /**
     * Set Field type.
     *
     * @param string $type
     *
     * @return string
     */
    public function setType($type = null)
    {
        if (isset($type)) {
            if (isset($this->fieldsArray[$type])) {
                $this->type = $type;
            } else {
                throw new InvalidArgumentException("Invalid type '$type'");
            }
        }

        return $this->type;
    }

    /**
     * Get Field type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Field properties.
     *
     * @param array $properties
     *
     * @return self
     */
    public function setProperties($properties = [])
    {
        if (is_array($properties)) {
            foreach (array_keys($properties) as $propkey) {
                if (!(isset($this->fieldsArray[$this->type]['properties'][$propkey]))) {
                    throw new InvalidArgumentException("Invalid property '$propkey'");
                }
            }
            $this->properties = array_merge($this->properties, $properties);
        }

        return $this->properties;
    }

    /**
     * Get Field properties.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set Field options.
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions($options = [])
    {
        if (is_array($options)) {
            foreach (array_keys($options) as $optionkey) {
                if (!(isset($this->fieldsArray[$this->type]['options'][$optionkey])) && substr($optionkey, 0, 1) !== '\\') {
                    throw new InvalidArgumentException("Invalid option '$optionkey', possible values are " . implode(', ', $this->fieldsArray[$this->type]['options']));
                }
            }
            $this->options = array_merge($this->options, $options);
        }

        return $this->options;
    }

    /**
     * Get Field properties.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set Field text.
     *
     * @param null|string|TextRun $text
     *
     * @return null|string|TextRun
     */
    public function setText($text = null)
    {
        if (isset($text)) {
            if (is_string($text) || $text instanceof TextRun) {
                $this->text = $text;
            } else {
                throw new InvalidArgumentException('Invalid text');
            }
        }

        return $this->text;
    }

    /**
     * Get Field text.
     *
     * @return string|TextRun
     */
    public function getText()
    {
        return $this->text;
    }
}
