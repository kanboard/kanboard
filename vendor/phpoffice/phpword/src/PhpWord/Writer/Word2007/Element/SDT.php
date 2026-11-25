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

namespace PhpOffice\PhpWord\Writer\Word2007\Element;

use PhpOffice\PhpWord\Element\SDT as SDTElement;
use PhpOffice\PhpWord\Shared\XMLWriter;

/**
 * Structured document tag element writer.
 *
 * @since 0.12.0
 * @see  http://www.datypic.com/sc/ooxml/t-w_CT_SdtBlock.html
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
class SDT extends Text
{
    /**
     * Write element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof SDTElement) {
            return;
        }
        $type = $element->getType();
        $writeFormField = "write{$type}";
        $alias = $element->getAlias();
        $tag = $element->getTag();
        $value = $element->getValue();
        if ($value === null) {
            $value = 'Pick value';
        }

        $this->startElementP();

        $xmlWriter->startElement('w:sdt');

        // Properties
        $xmlWriter->startElement('w:sdtPr');
        $xmlWriter->writeElementIf($alias != null, 'w:alias', 'w:val', $alias);
        $xmlWriter->writeElementBlock('w:lock', 'w:val', 'sdtLocked');
        $xmlWriter->writeElementBlock('w:id', 'w:val', mt_rand(100000000, 999999999));
        $xmlWriter->writeElementIf($tag != null, 'w:tag', 'w:val', $tag);
        $this->$writeFormField($xmlWriter, $element);
        $xmlWriter->endElement(); // w:sdtPr

        // Content
        $xmlWriter->startElement('w:sdtContent');
        $xmlWriter->startElement('w:r');
        $xmlWriter->writeElement('w:t', $value);
        $xmlWriter->endElement(); // w:r
        $xmlWriter->endElement(); // w:sdtContent

        $xmlWriter->endElement(); // w:sdt

        $this->endElementP(); // w:p
    }

    /**
     * Write text.
     *
     * @see  http://www.datypic.com/sc/ooxml/t-w_CT_SdtText.html
     */
    private function writePlainText(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement('w:text');
        $xmlWriter->endElement(); // w:text
    }

    /**
     * Write combo box.
     *
     * @see  http://www.datypic.com/sc/ooxml/t-w_CT_SdtComboBox.html
     */
    private function writeComboBox(XMLWriter $xmlWriter, SDTElement $element): void
    {
        $type = $element->getType();
        $listItems = $element->getListItems();

        $xmlWriter->startElement("w:{$type}");
        foreach ($listItems as $key => $val) {
            $xmlWriter->writeElementBlock('w:listItem', ['w:value' => $key, 'w:displayText' => $val]);
        }
        $xmlWriter->endElement(); // w:{$type}
    }

    /**
     * Write drop down list.
     *
     * @see  http://www.datypic.com/sc/ooxml/t-w_CT_SdtDropDownList.html
     */
    private function writeDropDownList(XMLWriter $xmlWriter, SDTElement $element): void
    {
        $this->writeComboBox($xmlWriter, $element);
    }

    /**
     * Write date.
     *
     * @see  http://www.datypic.com/sc/ooxml/t-w_CT_SdtDate.html
     */
    private function writeDate(XMLWriter $xmlWriter, SDTElement $element): void
    {
        $type = $element->getType();

        $xmlWriter->startElement("w:{$type}");
        $xmlWriter->writeElementBlock('w:dateFormat', 'w:val', 'd/M/yyyy');
        $xmlWriter->writeElementBlock('w:lid', 'w:val', 'en-US');
        $xmlWriter->writeElementBlock('w:storeMappedDataAs', 'w:val', 'dateTime');
        $xmlWriter->writeElementBlock('w:calendar', 'w:val', 'gregorian');
        $xmlWriter->endElement(); // w:date
    }
}
