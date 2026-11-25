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

namespace PhpOffice\PhpWord\Shared;

use DOMAttr;
use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;
use PhpOffice\PhpWord\ComplexType\RubyProperties;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Row;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\NumberFormat;
use PhpOffice\PhpWord\Style\Paragraph;

/**
 * Common Html functions.
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod) For readWPNode
 */
class Html
{
    private const RGB_REGEXP = '/^\s*rgb\s*[(]\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*[)]\s*$/';

    protected static $listIndex = 0;

    protected static $xpath;

    protected static $options;

    /**
     * @var Css
     */
    protected static $css;

    /**
     * Add HTML parts.
     *
     * Note: $stylesheet parameter is removed to avoid PHPMD error for unused parameter
     * Warning: Do not pass user-generated HTML here, as that would allow an attacker to read arbitrary
     * files or perform server-side request forgery by passing local file paths or URLs in <img>.
     *
     * @param AbstractContainer $element Where the parts need to be added
     * @param string $html The code to parse
     * @param bool $fullHTML If it's a full HTML, no need to add 'body' tag
     * @param bool $preserveWhiteSpace If false, the whitespaces between nodes will be removed
     */
    public static function addHtml($element, $html, $fullHTML = false, $preserveWhiteSpace = true, $options = null): void
    {
        /*
         * @todo parse $stylesheet for default styles.  Should result in an array based on id, class and element,
         * which could be applied when such an element occurs in the parseNode function.
         */
        static::$options = $options;

        // Preprocess: remove all line ends, decode HTML entity,
        // fix ampersand and angle brackets and add body tag for HTML fragments
        $html = str_replace(["\n", "\r"], '', $html);
        $html = str_replace(['&lt;', '&gt;', '&amp;', '&quot;'], ['_lt_', '_gt_', '_amp_', '_quot_'], $html);
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        $html = str_replace('&', '&amp;', $html);
        $html = str_replace(['_lt_', '_gt_', '_amp_', '_quot_'], ['&lt;', '&gt;', '&amp;', '&quot;'], $html);

        if (false === $fullHTML) {
            $html = '<body>' . $html . '</body>';
        }

        // Load DOM
        if (\PHP_VERSION_ID < 80000) {
            $orignalLibEntityLoader = libxml_disable_entity_loader(true);
        }
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = $preserveWhiteSpace;
        $dom->loadXML($html);
        static::$xpath = new DOMXPath($dom);
        $node = $dom->getElementsByTagName('body');

        static::parseNode($node->item(0), $element);
        if (\PHP_VERSION_ID < 80000) {
            libxml_disable_entity_loader($orignalLibEntityLoader);
        }
    }

    /**
     * parse Inline style of a node.
     *
     * @param DOMNode $node Node to check on attributes and to compile a style array
     * @param array<string, mixed> $styles is supplied, the inline style attributes are added to the already existing style
     *
     * @return array
     */
    protected static function parseInlineStyle($node, $styles = [])
    {
        if (XML_ELEMENT_NODE == $node->nodeType) {
            $attributes = $node->attributes; // get all the attributes(eg: id, class)

            $attributeDir = $attributes->getNamedItem('dir');
            $attributeDirValue = $attributeDir ? $attributeDir->nodeValue : '';
            $bidi = $attributeDirValue === 'rtl';
            foreach ($attributes as $attribute) {
                $val = $attribute->value;
                switch (strtolower($attribute->name)) {
                    case 'align':
                        $styles['alignment'] = self::mapAlign(trim($val), $bidi);

                        break;
                    case 'lang':
                        $styles['lang'] = $val;

                        break;
                    case 'width':
                        // tables, cells
                        $val = $val === 'auto' ? '100%' : $val;
                        if (false !== strpos($val, '%')) {
                            // e.g. <table width="100%"> or <td width="50%">
                            $styles['width'] = (int) $val * 50;
                            $styles['unit'] = \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT;
                        } else {
                            // e.g. <table width="250> where "250" = 250px (always pixels)
                            $styles['width'] = Converter::pixelToTwip(self::convertHtmlSize($val));
                            $styles['unit'] = \PhpOffice\PhpWord\SimpleType\TblWidth::TWIP;
                        }

                        break;
                    case 'cellspacing':
                        // tables e.g. <table cellspacing="2">,  where "2" = 2px (always pixels)
                        $styles['cellSpacing'] = Converter::pixelToTwip(self::convertHtmlSize($val));

                        break;
                    case 'bgcolor':
                        // tables, rows, cells e.g. <tr bgColor="#FF0000">
                        $styles['bgColor'] = self::convertRgb($val);

                        break;
                    case 'valign':
                        // cells e.g. <td valign="middle">
                        if (preg_match('#(?:top|bottom|middle|baseline)#i', $val, $matches)) {
                            $styles['valign'] = self::mapAlignVertical($matches[0]);
                        }

                        break;
                }
            }

            $attributeIdentifier = $attributes->getNamedItem('id');
            if ($attributeIdentifier && self::$css) {
                $styles = self::parseStyleDeclarations(self::$css->getStyle('#' . $attributeIdentifier->nodeValue), $styles);
            }

            $attributeClass = $attributes->getNamedItem('class');
            if ($attributeClass) {
                if (self::$css) {
                    $styles = self::parseStyleDeclarations(self::$css->getStyle('.' . $attributeClass->nodeValue), $styles);
                }
                $styles['className'] = $attributeClass->nodeValue;
            }

            $attributeStyle = $attributes->getNamedItem('style');
            if ($attributeStyle) {
                $styles = self::parseStyle($attributeStyle, $styles);
            }
        }

        return $styles;
    }

    /**
     * Parse a node and add a corresponding element to the parent element.
     *
     * @param DOMNode $node node to parse
     * @param AbstractContainer $element object to add an element corresponding with the node
     * @param array $styles Array with all styles
     * @param array $data Array to transport data to a next level in the DOM tree, for example level of listitems
     */
    protected static function parseNode($node, $element, $styles = [], $data = []): void
    {
        if ($node->nodeName == 'style') {
            self::$css = new Css($node->textContent);
            self::$css->process();

            return;
        }

        // Populate styles array
        $styleTypes = ['font', 'paragraph', 'list', 'table', 'row', 'cell'];
        foreach ($styleTypes as $styleType) {
            if (!isset($styles[$styleType])) {
                $styles[$styleType] = [];
            }
        }

        // Node mapping table
        $nodes = [
            // $method               $node   $element    $styles     $data   $argument1      $argument2
            'p' => ['Paragraph',     $node,  $element,   $styles,    null,   null,           null],
            'h1' => ['Heading',      $node,  $element,   $styles,    null,   'Heading1',     null],
            'h2' => ['Heading',      $node,  $element,   $styles,    null,   'Heading2',     null],
            'h3' => ['Heading',      $node,  $element,   $styles,    null,   'Heading3',     null],
            'h4' => ['Heading',      $node,  $element,   $styles,    null,   'Heading4',     null],
            'h5' => ['Heading',      $node,  $element,   $styles,    null,   'Heading5',     null],
            'h6' => ['Heading',      $node,  $element,   $styles,    null,   'Heading6',     null],
            '#text' => ['Text',      $node,  $element,   $styles,    null,   null,           null],
            'strong' => ['Property', null,   null,       $styles,    null,   'bold',         true],
            'b' => ['Property',    null,   null,       $styles,    null,   'bold',         true],
            'em' => ['Property',    null,   null,       $styles,    null,   'italic',       true],
            'i' => ['Property',    null,   null,       $styles,    null,   'italic',       true],
            'u' => ['Property',    null,   null,       $styles,    null,   'underline',    'single'],
            'sup' => ['Property',    null,   null,       $styles,    null,   'superScript',  true],
            'sub' => ['Property',    null,   null,       $styles,    null,   'subScript',    true],
            'span' => ['Span',        $node,  null,       $styles,    null,   null,           null],
            'font' => ['Span',        $node,  null,       $styles,    null,   null,           null],
            'table' => ['Table',       $node,  $element,   $styles,    null,   null,           null],
            'tr' => ['Row',         $node,  $element,   $styles,    null,   null,           null],
            'td' => ['Cell',        $node,  $element,   $styles,    null,   null,           null],
            'th' => ['Cell',        $node,  $element,   $styles,    null,   null,           null],
            'ul' => ['List',        $node,  $element,   $styles,    $data,  null,           null],
            'ol' => ['List',        $node,  $element,   $styles,    $data,  null,           null],
            'li' => ['ListItem',    $node,  $element,   $styles,    $data,  null,           null],
            'img' => ['Image',       $node,  $element,   $styles,    null,   null,           null],
            'br' => ['LineBreak',   null,   $element,   $styles,    null,   null,           null],
            'a' => ['Link',        $node,  $element,   $styles,    null,   null,           null],
            'input' => ['Input',       $node,  $element,   $styles,    null,   null,           null],
            'hr' => ['HorizRule',   $node,  $element,   $styles,    null,   null,           null],
            'ruby' => ['Ruby',   $node,  $element,   $styles,    null,   null,           null],
        ];

        $newElement = null;
        $keys = ['node', 'element', 'styles', 'data', 'argument1', 'argument2'];

        if (isset($nodes[$node->nodeName])) {
            // Execute method based on node mapping table and return $newElement or null
            // Arguments are passed by reference
            $arguments = [];
            $args = [];
            [$method, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5]] = $nodes[$node->nodeName];
            for ($i = 0; $i <= 5; ++$i) {
                if ($args[$i] !== null) {
                    $arguments[$keys[$i]] = &$args[$i];
                }
            }
            $method = "parse{$method}";
            $newElement = call_user_func_array(['PhpOffice\PhpWord\Shared\Html', $method], array_values($arguments));

            // Retrieve back variables from arguments
            foreach ($keys as $key) {
                if (array_key_exists($key, $arguments)) {
                    $$key = $arguments[$key];
                }
            }
        }

        if ($newElement === null) {
            $newElement = $element;
        }

        static::parseChildNodes($node, $newElement, $styles, $data);
    }

    /**
     * Parse child nodes.
     *
     * @param DOMNode $node
     * @param AbstractContainer|Row|Table $element
     * @param array $styles
     * @param array $data
     */
    protected static function parseChildNodes($node, $element, $styles, $data): void
    {
        if ('li' != $node->nodeName) {
            $cNodes = $node->childNodes;
            if (!empty($cNodes)) {
                foreach ($cNodes as $cNode) {
                    if ($element instanceof AbstractContainer || $element instanceof Table || $element instanceof Row) {
                        self::parseNode($cNode, $element, $styles, $data);
                    }
                }
            }
        }
    }

    /**
     * Parse paragraph node.
     *
     * @param DOMNode $node
     * @param AbstractContainer $element
     * @param array &$styles
     *
     * @return \PhpOffice\PhpWord\Element\PageBreak|TextRun
     */
    protected static function parseParagraph($node, $element, &$styles)
    {
        $styles['paragraph'] = self::recursiveParseStylesInHierarchy($node, $styles['paragraph']);
        if (isset($styles['paragraph']['isPageBreak']) && $styles['paragraph']['isPageBreak']) {
            return $element->addPageBreak();
        }

        return $element->addTextRun($styles['paragraph']);
    }

    /**
     * Parse input node.
     *
     * @param DOMNode $node
     * @param AbstractContainer $element
     * @param array &$styles
     */
    protected static function parseInput($node, $element, &$styles): void
    {
        $attributes = $node->attributes;
        if (null === $attributes->getNamedItem('type')) {
            return;
        }

        $inputType = $attributes->getNamedItem('type')->nodeValue;
        switch ($inputType) {
            case 'checkbox':
                $checked = ($checked = $attributes->getNamedItem('checked')) && $checked->nodeValue === 'true' ? true : false;
                $textrun = $element->addTextRun($styles['paragraph']);
                $textrun->addFormField('checkbox')->setValue($checked);

                break;
        }
    }

    /**
     * Parse heading node.
     *
     * @param string $argument1 Name of heading style
     *
     * @todo Think of a clever way of defining header styles, now it is only based on the assumption, that
     * Heading1 - Heading6 are already defined somewhere
     */
    protected static function parseHeading(DOMNode $node, AbstractContainer $element, array &$styles, string $argument1): TextRun
    {
        $style = new Paragraph();
        $style->setStyleName($argument1);
        $style->setStyleByArray(self::parseInlineStyle($node, $styles['paragraph']));

        return $element->addTextRun($style);
    }

    /**
     * Parse text node.
     *
     * @param DOMNode $node
     * @param AbstractContainer $element
     * @param array &$styles
     */
    protected static function parseText($node, $element, &$styles): void
    {
        $styles['font'] = self::recursiveParseStylesInHierarchy($node, $styles['font']);

        //alignment applies on paragraph, not on font. Let's copy it there
        if (isset($styles['font']['alignment']) && is_array($styles['paragraph'])) {
            $styles['paragraph']['alignment'] = $styles['font']['alignment'];
        }

        if (is_callable([$element, 'addText'])) {
            $element->addText($node->nodeValue, $styles['font'], $styles['paragraph']);
        }
    }

    /**
     * Parse property node.
     *
     * @param array &$styles
     * @param string $argument1 Style name
     * @param string $argument2 Style value
     */
    protected static function parseProperty(&$styles, $argument1, $argument2): void
    {
        $styles['font'][$argument1] = $argument2;
    }

    /**
     * Parse span node.
     *
     * @param DOMNode $node
     * @param array &$styles
     */
    protected static function parseSpan($node, &$styles): void
    {
        self::parseInlineStyle($node, $styles['font']);
    }

    /**
     * Parse table node.
     *
     * @param DOMNode $node
     * @param AbstractContainer $element
     * @param array &$styles
     *
     * @return Table $element
     *
     * @todo As soon as TableItem, RowItem and CellItem support relative width and height
     */
    protected static function parseTable($node, $element, &$styles)
    {
        $elementStyles = self::parseInlineStyle($node, $styles['table']);

        $newElement = $element->addTable($elementStyles);

        // Add style name from CSS Class
        if (isset($elementStyles['className'])) {
            $newElement->getStyle()->setStyleName($elementStyles['className']);
        }

        $attributes = $node->attributes;
        if ($attributes->getNamedItem('border')) {
            $border = (int) $attributes->getNamedItem('border')->nodeValue;
            $newElement->getStyle()->setBorderSize(Converter::pixelToTwip($border));
        }

        return $newElement;
    }

    /**
     * Parse a table row.
     *
     * @param DOMNode $node
     * @param Table $element
     * @param array &$styles
     *
     * @return Row $element
     */
    protected static function parseRow($node, $element, &$styles)
    {
        $rowStyles = self::parseInlineStyle($node, $styles['row']);
        if ($node->parentNode->nodeName == 'thead') {
            $rowStyles['tblHeader'] = true;
        }

        // set cell height to control row heights
        $height = $rowStyles['height'] ?? null;
        unset($rowStyles['height']); // would not apply

        return $element->addRow($height, $rowStyles);
    }

    /**
     * Parse table cell.
     *
     * @param DOMNode $node
     * @param Table $element
     * @param array &$styles
     *
     * @return \PhpOffice\PhpWord\Element\Cell|TextRun $element
     */
    protected static function parseCell($node, $element, &$styles)
    {
        $cellStyles = self::recursiveParseStylesInHierarchy($node, $styles['cell']);

        $colspan = $node->getAttribute('colspan');
        if (!empty($colspan)) {
            $cellStyles['gridSpan'] = $colspan - 0;
        }

        // set cell width to control column widths
        $width = $cellStyles['width'] ?? null;
        unset($cellStyles['width']); // would not apply
        $cell = $element->addCell($width, $cellStyles);

        if (self::shouldAddTextRun($node)) {
            return $cell->addTextRun(self::filterOutNonInheritedStyles(self::parseInlineStyle($node, $styles['paragraph'])));
        }

        return $cell;
    }

    /**
     * Checks if $node contains an HTML element that cannot be added to TextRun.
     *
     * @return bool Returns true if the node contains an HTML element that cannot be added to TextRun
     */
    protected static function shouldAddTextRun(DOMNode $node)
    {
        $containsBlockElement = self::$xpath->query('.//table|./p|./ul|./ol|./h1|./h2|./h3|./h4|./h5|./h6', $node)->length > 0;
        if ($containsBlockElement) {
            return false;
        }

        return true;
    }

    /**
     * Recursively parses styles on parent nodes
     * TODO if too slow, add caching of parent nodes, !! everything is static here so watch out for concurrency !!
     */
    protected static function recursiveParseStylesInHierarchy(DOMNode $node, array $style)
    {
        $parentStyle = [];
        if ($node->parentNode != null && XML_ELEMENT_NODE == $node->parentNode->nodeType) {
            $parentStyle = self::recursiveParseStylesInHierarchy($node->parentNode, []);
        }
        if ($node->nodeName === '#text') {
            $parentStyle = array_merge($parentStyle, $style);
        } else {
            $parentStyle = self::filterOutNonInheritedStyles($parentStyle);
        }
        $style = self::parseInlineStyle($node, $parentStyle);

        return $style;
    }

    /**
     * Removes non-inherited styles from array.
     */
    protected static function filterOutNonInheritedStyles(array $styles)
    {
        $nonInheritedStyles = [
            'borderSize',
            'borderTopSize',
            'borderRightSize',
            'borderBottomSize',
            'borderLeftSize',
            'borderColor',
            'borderTopColor',
            'borderRightColor',
            'borderBottomColor',
            'borderLeftColor',
            'borderStyle',
            'spaceAfter',
            'spaceBefore',
            'underline',
            'strikethrough',
            'hidden',
        ];

        $styles = array_diff_key($styles, array_flip($nonInheritedStyles));

        return $styles;
    }

    /**
     * Parse list node.
     *
     * @param DOMNode $node
     * @param AbstractContainer $element
     * @param array &$styles
     * @param array &$data
     */
    protected static function parseList($node, $element, &$styles, &$data)
    {
        $isOrderedList = $node->nodeName === 'ol';
        if (isset($data['listdepth'])) {
            ++$data['listdepth'];
        } else {
            $data['listdepth'] = 0;
            $styles['list'] = 'listStyle_' . self::$listIndex++;
            $style = $element->getPhpWord()->addNumberingStyle($styles['list'], self::getListStyle($isOrderedList));

            // extract attributes start & type e.g. <ol type="A" start="3">
            $start = 0;
            $type = '';
            foreach ($node->attributes as $attribute) {
                switch ($attribute->name) {
                    case 'start':
                        $start = (int) $attribute->value;

                        break;
                    case 'type':
                        $type = $attribute->value;

                        break;
                }
            }

            $levels = $style->getLevels();
            /** @var \PhpOffice\PhpWord\Style\NumberingLevel */
            $level = $levels[0];
            if ($start > 0) {
                $level->setStart($start);
            }
            $type = $type ? self::mapListType($type) : null;
            if ($type) {
                $level->setFormat($type);
            }
        }
        if ($node->parentNode->nodeName === 'li') {
            return $element->getParent();
        }
    }

    /**
     * @param bool $isOrderedList
     *
     * @return array
     */
    protected static function getListStyle($isOrderedList)
    {
        if ($isOrderedList) {
            return [
                'type' => 'multilevel',
                'levels' => [
                    ['format' => NumberFormat::DECIMAL,      'text' => '%1.', 'alignment' => 'left',  'tabPos' => 720,  'left' => 720,  'hanging' => 360],
                    ['format' => NumberFormat::LOWER_LETTER, 'text' => '%2.', 'alignment' => 'left',  'tabPos' => 1440, 'left' => 1440, 'hanging' => 360],
                    ['format' => NumberFormat::LOWER_ROMAN,  'text' => '%3.', 'alignment' => 'right', 'tabPos' => 2160, 'left' => 2160, 'hanging' => 180],
                    ['format' => NumberFormat::DECIMAL,      'text' => '%4.', 'alignment' => 'left',  'tabPos' => 2880, 'left' => 2880, 'hanging' => 360],
                    ['format' => NumberFormat::LOWER_LETTER, 'text' => '%5.', 'alignment' => 'left',  'tabPos' => 3600, 'left' => 3600, 'hanging' => 360],
                    ['format' => NumberFormat::LOWER_ROMAN,  'text' => '%6.', 'alignment' => 'right', 'tabPos' => 4320, 'left' => 4320, 'hanging' => 180],
                    ['format' => NumberFormat::DECIMAL,      'text' => '%7.', 'alignment' => 'left',  'tabPos' => 5040, 'left' => 5040, 'hanging' => 360],
                    ['format' => NumberFormat::LOWER_LETTER, 'text' => '%8.', 'alignment' => 'left',  'tabPos' => 5760, 'left' => 5760, 'hanging' => 360],
                    ['format' => NumberFormat::LOWER_ROMAN,  'text' => '%9.', 'alignment' => 'right', 'tabPos' => 6480, 'left' => 6480, 'hanging' => 180],
                ],
            ];
        }

        return [
            'type' => 'hybridMultilevel',
            'levels' => [
                ['format' => NumberFormat::BULLET, 'text' => '•', 'alignment' => 'left', 'tabPos' => 720,  'left' => 720,  'hanging' => 360, 'font' => 'Symbol',      'hint' => 'default'],
                ['format' => NumberFormat::BULLET, 'text' => '◦',  'alignment' => 'left', 'tabPos' => 1440, 'left' => 1440, 'hanging' => 360, 'font' => 'Courier New', 'hint' => 'default'],
                ['format' => NumberFormat::BULLET, 'text' => '•', 'alignment' => 'left', 'tabPos' => 2160, 'left' => 2160, 'hanging' => 360, 'font' => 'Wingdings',   'hint' => 'default'],
                ['format' => NumberFormat::BULLET, 'text' => '•', 'alignment' => 'left', 'tabPos' => 2880, 'left' => 2880, 'hanging' => 360, 'font' => 'Symbol',      'hint' => 'default'],
                ['format' => NumberFormat::BULLET, 'text' => '◦',  'alignment' => 'left', 'tabPos' => 3600, 'left' => 3600, 'hanging' => 360, 'font' => 'Courier New', 'hint' => 'default'],
                ['format' => NumberFormat::BULLET, 'text' => '•', 'alignment' => 'left', 'tabPos' => 4320, 'left' => 4320, 'hanging' => 360, 'font' => 'Wingdings',   'hint' => 'default'],
                ['format' => NumberFormat::BULLET, 'text' => '•', 'alignment' => 'left', 'tabPos' => 5040, 'left' => 5040, 'hanging' => 360, 'font' => 'Symbol',      'hint' => 'default'],
                ['format' => NumberFormat::BULLET, 'text' => '◦',  'alignment' => 'left', 'tabPos' => 5760, 'left' => 5760, 'hanging' => 360, 'font' => 'Courier New', 'hint' => 'default'],
                ['format' => NumberFormat::BULLET, 'text' => '•', 'alignment' => 'left', 'tabPos' => 6480, 'left' => 6480, 'hanging' => 360, 'font' => 'Wingdings',   'hint' => 'default'],
            ],
        ];
    }

    /**
     * Parse list item node.
     *
     * @param DOMNode $node
     * @param AbstractContainer $element
     * @param array &$styles
     * @param array $data
     *
     * @todo This function is almost the same like `parseChildNodes`. Merged?
     * @todo As soon as ListItem inherits from AbstractContainer or TextRun delete parsing part of childNodes
     */
    protected static function parseListItem($node, $element, &$styles, $data): void
    {
        $cNodes = $node->childNodes;
        if (!empty($cNodes)) {
            $listRun = $element->addListItemRun($data['listdepth'], $styles['list'], $styles['paragraph']);
            foreach ($cNodes as $cNode) {
                self::parseNode($cNode, $listRun, $styles, $data);
            }
        }
    }

    /**
     * Parse style.
     *
     * @param DOMAttr $attribute
     * @param array $styles
     *
     * @return array
     */
    protected static function parseStyle($attribute, $styles)
    {
        $properties = explode(';', trim($attribute->value, " \t\n\r\0\x0B;"));

        $selectors = [];
        foreach ($properties as $property) {
            [$cKey, $cValue] = array_pad(explode(':', $property, 2), 2, null);
            $selectors[strtolower(trim($cKey))] = trim($cValue ?? '');
        }

        return self::parseStyleDeclarations($selectors, $styles);
    }

    protected static function parseStyleDeclarations(array $selectors, array $styles)
    {
        $bidi = ($selectors['direction'] ?? '') === 'rtl';
        foreach ($selectors as $property => $value) {
            switch ($property) {
                case 'text-decoration':
                    switch ($value) {
                        case 'underline':
                            $styles['underline'] = 'single';

                            break;
                        case 'line-through':
                            $styles['strikethrough'] = true;

                            break;
                    }

                    break;
                case 'text-align':
                    $styles['alignment'] = self::mapAlign($value, $bidi);

                    break;
                case 'ruby-align':
                    $styles['rubyAlignment'] = self::mapRubyAlign($value);

                    break;
                case 'display':
                    $styles['hidden'] = $value === 'none' || $value === 'hidden';

                    break;
                case 'direction':
                    $styles['rtl'] = $value === 'rtl';
                    $styles['bidi'] = $value === 'rtl';

                    break;
                case 'font-size':
                    $styles['size'] = Converter::cssToPoint($value);

                    break;
                case 'font-family':
                    $value = array_map('trim', explode(',', $value));
                    $styles['name'] = ucwords($value[0]);

                    break;
                case 'color':
                    $styles['color'] = self::convertRgb($value);

                    break;
                case 'background-color':
                    $styles['bgColor'] = self::convertRgb($value);

                    break;
                case 'line-height':
                    $matches = [];
                    if ($value === 'normal' || $value === 'inherit') {
                        $spacingLineRule = \PhpOffice\PhpWord\SimpleType\LineSpacingRule::AUTO;
                        $spacing = 0;
                    } elseif (preg_match('/([0-9]+\.?[0-9]*[a-z]+)/', $value, $matches)) {
                        //matches number with a unit, e.g. 12px, 15pt, 20mm, ...
                        $spacingLineRule = \PhpOffice\PhpWord\SimpleType\LineSpacingRule::EXACT;
                        $spacing = Converter::cssToTwip($matches[1]);
                    } elseif (preg_match('/([0-9]+)%/', $value, $matches)) {
                        //matches percentages
                        $spacingLineRule = \PhpOffice\PhpWord\SimpleType\LineSpacingRule::AUTO;
                        //we are subtracting 1 line height because the Spacing writer is adding one line
                        $spacing = ((((int) $matches[1]) / 100) * Paragraph::LINE_HEIGHT) - Paragraph::LINE_HEIGHT;
                    } else {
                        //any other, wich is a multiplier. E.g. 1.2
                        $spacingLineRule = \PhpOffice\PhpWord\SimpleType\LineSpacingRule::AUTO;
                        //we are subtracting 1 line height because the Spacing writer is adding one line
                        $spacing = ($value * Paragraph::LINE_HEIGHT) - Paragraph::LINE_HEIGHT;
                    }
                    $styles['spacingLineRule'] = $spacingLineRule;
                    $styles['line-spacing'] = $spacing;

                    break;
                case 'letter-spacing':
                    $styles['letter-spacing'] = Converter::cssToTwip($value);

                    break;
                case 'text-indent':
                    $styles['indentation']['firstLine'] = Converter::cssToTwip($value);

                    break;
                case 'font-weight':
                    $tValue = false;
                    if (preg_match('#bold#', $value)) {
                        $tValue = true; // also match bolder
                    }
                    $styles['bold'] = $tValue;

                    break;
                case 'font-style':
                    $tValue = false;
                    if (preg_match('#(?:italic|oblique)#', $value)) {
                        $tValue = true;
                    }
                    $styles['italic'] = $tValue;

                    break;
                case 'font-variant':
                    $tValue = false;
                    if (preg_match('#small-caps#', $value)) {
                        $tValue = true;
                    }
                    $styles['smallCaps'] = $tValue;

                    break;
                case 'margin':
                    $value = Converter::cssToTwip($value);
                    $styles['spaceBefore'] = $value;
                    $styles['spaceAfter'] = $value;

                    break;
                case 'margin-top':
                    // BC change: up to ver. 0.17.0 incorrectly converted to points - Converter::cssToPoint($value)
                    $styles['spaceBefore'] = Converter::cssToTwip($value);

                    break;
                case 'margin-bottom':
                    // BC change: up to ver. 0.17.0 incorrectly converted to points - Converter::cssToPoint($value)
                    $styles['spaceAfter'] = Converter::cssToTwip($value);

                    break;

                case 'padding':
                    $valueTop = $valueRight = $valueBottom = $valueLeft = null;
                    $cValue = preg_replace('# +#', ' ', trim($value));
                    $paddingArr = explode(' ', $cValue);
                    $countParams = count($paddingArr);
                    if ($countParams == 1) {
                        $valueTop = $valueRight = $valueBottom = $valueLeft = $paddingArr[0];
                    } elseif ($countParams == 2) {
                        $valueTop = $valueBottom = $paddingArr[0];
                        $valueRight = $valueLeft = $paddingArr[1];
                    } elseif ($countParams == 3) {
                        $valueTop = $paddingArr[0];
                        $valueRight = $valueLeft = $paddingArr[1];
                        $valueBottom = $paddingArr[2];
                    } elseif ($countParams == 4) {
                        $valueTop = $paddingArr[0];
                        $valueRight = $paddingArr[1];
                        $valueBottom = $paddingArr[2];
                        $valueLeft = $paddingArr[3];
                    }
                    if ($valueTop !== null) {
                        $styles['paddingTop'] = Converter::cssToTwip($valueTop);
                    }
                    if ($valueRight !== null) {
                        $styles['paddingRight'] = Converter::cssToTwip($valueRight);
                    }
                    if ($valueBottom !== null) {
                        $styles['paddingBottom'] = Converter::cssToTwip($valueBottom);
                    }
                    if ($valueLeft !== null) {
                        $styles['paddingLeft'] = Converter::cssToTwip($valueLeft);
                    }

                    break;
                case 'padding-top':
                    $styles['paddingTop'] = Converter::cssToTwip($value);

                    break;
                case 'padding-right':
                    $styles['paddingRight'] = Converter::cssToTwip($value);

                    break;
                case 'padding-bottom':
                    $styles['paddingBottom'] = Converter::cssToTwip($value);

                    break;
                case 'padding-left':
                    $styles['paddingLeft'] = Converter::cssToTwip($value);

                    break;

                case 'border-color':
                    self::mapBorderColor($styles, $value);

                    break;
                case 'border-width':
                    $styles['borderSize'] = Converter::cssToPoint($value);

                    break;
                case 'border-style':
                    $styles['borderStyle'] = self::mapBorderStyle($value);

                    break;
                case 'width':
                    if (preg_match('/([0-9]+[a-z]+)/', $value, $matches)) {
                        $styles['width'] = Converter::cssToTwip($matches[1]);
                        $styles['unit'] = \PhpOffice\PhpWord\SimpleType\TblWidth::TWIP;
                    } elseif (preg_match('/([0-9]+)%/', $value, $matches)) {
                        $styles['width'] = $matches[1] * 50;
                        $styles['unit'] = \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT;
                    } elseif (preg_match('/([0-9]+)/', $value, $matches)) {
                        $styles['width'] = $matches[1];
                        $styles['unit'] = \PhpOffice\PhpWord\SimpleType\TblWidth::AUTO;
                    }

                    break;
                case 'height':
                    $styles['height'] = Converter::cssToTwip($value);
                    $styles['exactHeight'] = true;

                    break;
                case 'border':
                case 'border-top':
                case 'border-bottom':
                case 'border-right':
                case 'border-left':
                    // must have exact order [width color style], e.g. "1px #0011CC solid" or "2pt green solid"
                    // Word does not accept shortened hex colors e.g. #CCC, only full e.g. #CCCCCC
                    if (preg_match('/([0-9]+[^0-9]*)\s+(\#[a-fA-F0-9]+|[a-zA-Z]+)\s+([a-z]+)/', $value, $matches)) {
                        if (false !== strpos($property, '-')) {
                            $tmp = explode('-', $property);
                            $which = $tmp[1];
                            $which = ucfirst($which); // e.g. bottom -> Bottom
                        } else {
                            $which = '';
                        }
                        // Note - border width normalization:
                        // Width of border in Word is calculated differently than HTML borders, usually showing up too bold.
                        // Smallest 1px (or 1pt) appears in Word like 2-3px/pt in HTML once converted to twips.
                        // Therefore we need to normalize converted twip value to cca 1/2 of value.
                        // This may be adjusted, if better ratio or formula found.
                        // BC change: up to ver. 0.17.0 was $size converted to points - Converter::cssToPoint($size)
                        $size = Converter::cssToTwip($matches[1]);
                        $size = (int) ($size / 2);
                        // valid variants may be e.g. borderSize, borderTopSize, borderLeftColor, etc ..
                        $styles["border{$which}Size"] = $size; // twips
                        $styles["border{$which}Color"] = trim($matches[2], '#');
                        $styles["border{$which}Style"] = self::mapBorderStyle($matches[3]);
                    }

                    break;
                case 'vertical-align':
                    // https://developer.mozilla.org/en-US/docs/Web/CSS/vertical-align
                    if (preg_match('#(?:top|bottom|middle|sub|baseline)#i', $value, $matches)) {
                        $styles['valign'] = self::mapAlignVertical($matches[0]);
                    }

                    break;
                case 'page-break-after':
                    if ($value == 'always') {
                        $styles['isPageBreak'] = true;
                    }

                    break;
            }
        }

        return $styles;
    }

    /**
     * Parse image node.
     *
     * @param DOMNode $node
     * @param AbstractContainer $element
     *
     * @return \PhpOffice\PhpWord\Element\Image
     */
    protected static function parseImage($node, $element)
    {
        $style = [];
        $src = null;
        foreach ($node->attributes as $attribute) {
            switch ($attribute->name) {
                case 'src':
                    $src = $attribute->value;

                    break;
                case 'width':
                    $style['width'] = self::convertHtmlSize($attribute->value);
                    $style['unit'] = \PhpOffice\PhpWord\Style\Image::UNIT_PX;

                    break;
                case 'height':
                    $style['height'] = self::convertHtmlSize($attribute->value);
                    $style['unit'] = \PhpOffice\PhpWord\Style\Image::UNIT_PX;

                    break;
                case 'style':
                    $styleattr = explode(';', $attribute->value);
                    foreach ($styleattr as $attr) {
                        if (strpos($attr, ':')) {
                            [$k, $v] = explode(':', $attr);
                            switch ($k) {
                                case 'float':
                                    if (trim($v) == 'right') {
                                        $style['hPos'] = \PhpOffice\PhpWord\Style\Image::POS_RIGHT;
                                        $style['hPosRelTo'] = \PhpOffice\PhpWord\Style\Image::POS_RELTO_MARGIN; // inner section area
                                        $style['pos'] = \PhpOffice\PhpWord\Style\Image::POS_RELATIVE;
                                        $style['wrap'] = \PhpOffice\PhpWord\Style\Image::WRAP_TIGHT;
                                        $style['overlap'] = true;
                                    }
                                    if (trim($v) == 'left') {
                                        $style['hPos'] = \PhpOffice\PhpWord\Style\Image::POS_LEFT;
                                        $style['hPosRelTo'] = \PhpOffice\PhpWord\Style\Image::POS_RELTO_MARGIN; // inner section area
                                        $style['pos'] = \PhpOffice\PhpWord\Style\Image::POS_RELATIVE;
                                        $style['wrap'] = \PhpOffice\PhpWord\Style\Image::WRAP_TIGHT;
                                        $style['overlap'] = true;
                                    }

                                    break;
                            }
                        }
                    }

                    break;
            }
        }
        $originSrc = $src;
        if (strpos($src, 'data:image') !== false) {
            $tmpDir = Settings::getTempDir() . '/';

            $match = [];
            preg_match('/data:image\/(\w+);base64,(.+)/', $src, $match);
            if (!empty($match)) {
                $src = $imgFile = $tmpDir . uniqid() . '.' . $match[1];

                $ifp = fopen($imgFile, 'wb');

                if ($ifp !== false) {
                    fwrite($ifp, base64_decode($match[2]));
                    fclose($ifp);
                }
            }
        }
        $src = urldecode($src);

        if (!is_file($src)
            && null !== self::$options
            && isset(self::$options['IMG_SRC_SEARCH'], self::$options['IMG_SRC_REPLACE'])
        ) {
            $src = str_replace(self::$options['IMG_SRC_SEARCH'], self::$options['IMG_SRC_REPLACE'], $src);
        }

        if (!is_file($src)) {
            if ($imgBlob = @file_get_contents($src)) {
                $tmpDir = Settings::getTempDir() . '/';
                $match = [];
                preg_match('/.+\.(\w+)$/', $src, $match);
                $src = $tmpDir . uniqid();
                if (isset($match[1])) {
                    $src .= '.' . $match[1];
                }

                $ifp = fopen($src, 'wb');

                if ($ifp !== false) {
                    fwrite($ifp, $imgBlob);
                    fclose($ifp);
                }
            }
        }

        if (is_file($src)) {
            $newElement = $element->addImage($src, $style);
        } else {
            throw new Exception("Could not load image $originSrc");
        }

        return $newElement;
    }

    /**
     * Transforms a CSS border style into a word border style.
     *
     * @param string $cssBorderStyle
     *
     * @return null|string
     */
    protected static function mapBorderStyle($cssBorderStyle)
    {
        switch ($cssBorderStyle) {
            case 'none':
            case 'dashed':
            case 'dotted':
            case 'double':
                return $cssBorderStyle;
            default:
                return 'single';
        }
    }

    protected static function mapBorderColor(&$styles, $cssBorderColor): void
    {
        $numColors = substr_count($cssBorderColor, '#');
        if ($numColors === 1) {
            $styles['borderColor'] = trim($cssBorderColor, '#');
        } elseif ($numColors > 1) {
            $colors = explode(' ', $cssBorderColor);
            $borders = ['borderTopColor', 'borderRightColor', 'borderBottomColor', 'borderLeftColor'];
            for ($i = 0; $i < min(4, $numColors, count($colors)); ++$i) {
                $styles[$borders[$i]] = trim($colors[$i], '#');
            }
        }
    }

    /**
     * Transforms a HTML/CSS alignment into a \PhpOffice\PhpWord\SimpleType\Jc.
     *
     * @param string $cssAlignment
     * @param bool $bidi
     *
     * @return null|string
     */
    protected static function mapAlign($cssAlignment, $bidi)
    {
        switch ($cssAlignment) {
            case 'right':
                return $bidi ? Jc::START : Jc::END;
            case 'center':
                return Jc::CENTER;
            case 'justify':
                return Jc::BOTH;
            default:
                return $bidi ? Jc::END : Jc::START;
        }
    }

    /**
     * Transforms a HTML/CSS ruby alignment into a \PhpOffice\PhpWord\SimpleType\Jc.
     */
    protected static function mapRubyAlign(string $cssRubyAlignment): string
    {
        switch ($cssRubyAlignment) {
            case 'center':
                return RubyProperties::ALIGNMENT_CENTER;
            case 'start':
                return RubyProperties::ALIGNMENT_LEFT;
            case 'space-between':
                return RubyProperties::ALIGNMENT_DISTRIBUTE_SPACE;
            default:
                return '';
        }
    }

    /**
     * Transforms a HTML/CSS vertical alignment.
     *
     * @param string $alignment
     *
     * @return null|string
     */
    protected static function mapAlignVertical($alignment)
    {
        $alignment = strtolower($alignment);
        switch ($alignment) {
            case 'top':
            case 'baseline':
            case 'bottom':
                return $alignment;
            case 'middle':
                return 'center';
            case 'sub':
                return 'bottom';
            case 'text-top':
            case 'baseline':
                return 'top';
            default:
                // @discuss - which one should apply:
                // - Word uses default vert. alignment: top
                // - all browsers use default vert. alignment: middle
                // Returning empty string means attribute wont be set so use Word default (top).
                return '';
        }
    }

    /**
     * Map list style for ordered list.
     *
     * @param string $cssListType
     */
    protected static function mapListType($cssListType)
    {
        switch ($cssListType) {
            case 'a':
                return NumberFormat::LOWER_LETTER; // a, b, c, ..
            case 'A':
                return NumberFormat::UPPER_LETTER; // A, B, C, ..
            case 'i':
                return NumberFormat::LOWER_ROMAN; // i, ii, iii, iv, ..
            case 'I':
                return NumberFormat::UPPER_ROMAN; // I, II, III, IV, ..
            case '1':
            default:
                return NumberFormat::DECIMAL; // 1, 2, 3, ..
        }
    }

    /**
     * Parse line break.
     *
     * @param AbstractContainer $element
     */
    protected static function parseLineBreak($element): void
    {
        $element->addTextBreak();
    }

    /**
     * Parse link node.
     *
     * @param DOMNode $node
     * @param AbstractContainer $element
     * @param array $styles
     */
    protected static function parseLink($node, $element, &$styles)
    {
        $target = null;
        foreach ($node->attributes as $attribute) {
            switch ($attribute->name) {
                case 'href':
                    $target = $attribute->value;

                    break;
            }
        }
        $styles['font'] = self::parseInlineStyle($node, $styles['font']);

        if (empty($target)) {
            $target = '#';
        }

        if (strpos($target, '#') === 0 && strlen($target) > 1) {
            return $element->addLink(substr($target, 1), $node->textContent, $styles['font'], $styles['paragraph'], true);
        }

        return $element->addLink($target, $node->textContent, $styles['font'], $styles['paragraph']);
    }

    /**
     * Render horizontal rule
     * Note: Word rule is not the same as HTML's <hr> since it does not support width and thus neither alignment.
     *
     * @param DOMNode $node
     * @param AbstractContainer $element
     */
    protected static function parseHorizRule($node, $element): void
    {
        $styles = self::parseInlineStyle($node);

        // <hr> is implemented as an empty paragraph - extending 100% inside the section
        // Some properties may be controlled, e.g. <hr style="border-bottom: 3px #DDDDDD solid; margin-bottom: 0;">

        $fontStyle = $styles + ['size' => 3];

        $paragraphStyle = $styles + [
            'lineHeight' => 0.25, // multiply default line height - e.g. 1, 1.5 etc
            'spacing' => 0, // twip
            'spaceBefore' => 120, // twip, 240/2 (default line height)
            'spaceAfter' => 120, // twip
            'borderBottomSize' => empty($styles['line-height']) ? 1 : $styles['line-height'],
            'borderBottomColor' => empty($styles['color']) ? '000000' : $styles['color'],
            'borderBottomStyle' => 'single', // same as "solid"
        ];

        $element->addText('', $fontStyle, $paragraphStyle);

        // Notes: <hr/> cannot be:
        // - table - throws error "cannot be inside textruns", e.g. lists
        // - line - that is a shape, has different behaviour
        // - repeated text, e.g. underline "_", because of unpredictable line wrapping
    }

    /**
     * Parse ruby node.
     *
     * @param DOMNode $node
     * @param AbstractContainer $element
     * @param array $styles
     */
    protected static function parseRuby($node, $element, &$styles)
    {
        $rubyProperties = new RubyProperties();
        $baseTextRun = new TextRun($styles['paragraph']);
        $rubyTextRun = new TextRun(null);
        if ($node->hasAttributes()) {
            $langAttr = $node->attributes->getNamedItem('lang');
            if ($langAttr !== null) {
                $rubyProperties->setLanguageId($langAttr->textContent);
            }
            $styleAttr = $node->attributes->getNamedItem('style');
            if ($styleAttr !== null) {
                $styles = self::parseStyle($styleAttr, $styles['paragraph']);
                if (isset($styles['rubyAlignment']) && $styles['rubyAlignment'] !== '') {
                    $rubyProperties->setAlignment($styles['rubyAlignment']);
                }
                if (isset($styles['size']) && $styles['size'] !== '') {
                    $rubyProperties->setFontSizeForBaseText($styles['size']);
                }
                $baseTextRun->setParagraphStyle($styles);
            }
        }
        foreach ($node->childNodes as $child) {
            if ($child->nodeName === '#text') {
                $content = trim($child->textContent);
                if ($content !== '') {
                    $baseTextRun->addText($content);
                }
            } elseif ($child->nodeName === 'rt') {
                $rubyTextRun->addText(trim($child->textContent));
                if ($child->hasAttributes()) {
                    $styleAttr = $child->attributes->getNamedItem('style');
                    if ($styleAttr !== null) {
                        $styles = self::parseStyle($styleAttr, []);
                        if (isset($styles['size']) && $styles['size'] !== '') {
                            $rubyProperties->setFontFaceSize($styles['size']);
                        }
                        $rubyTextRun->setParagraphStyle($styles);
                    }
                }
            }
        }

        return $element->addRuby($baseTextRun, $rubyTextRun, $rubyProperties);
    }

    private static function convertRgb(string $rgb): string
    {
        if (preg_match(self::RGB_REGEXP, $rgb, $matches) === 1) {
            return sprintf('%02X%02X%02X', $matches[1], $matches[2], $matches[3]);
        }

        return trim($rgb, '# ');
    }

    /**
     * Transform HTML sizes (pt, px) in pixels.
     */
    protected static function convertHtmlSize(string $size): float
    {
        // pt
        if (false !== strpos($size, 'pt')) {
            return Converter::pointToPixel((float) str_replace('pt', '', $size));
        }

        // px
        if (false !== strpos($size, 'px')) {
            return (float) str_replace('px', '', $size);
        }

        return (float) $size;
    }
}
