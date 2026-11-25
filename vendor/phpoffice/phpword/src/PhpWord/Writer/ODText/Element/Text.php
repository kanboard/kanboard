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

namespace PhpOffice\PhpWord\Writer\ODText\Element;

use PhpOffice\PhpWord\Element\TrackChange;
use PhpOffice\PhpWord\Exception\Exception;

/**
 * Text element writer.
 *
 * @since 0.10.0
 */
class Text extends AbstractElement
{
    /**
     * Write element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\Text) {
            return;
        }
        $fontStyle = $element->getFontStyle();
        $paragraphStyle = $element->getParagraphStyle();

        // @todo Commented for TextRun. Should really checkout this value
        // $fStyleIsObject = ($fontStyle instanceof Font) ? true : false;
        //$fStyleIsObject = false;

        //if ($fStyleIsObject) {
        // Don't never be the case, because I browse all sections for cleaning all styles not declared
        //    throw new Exception('PhpWord : $fStyleIsObject wouldn\'t be an object');
        //}

        if (!$this->withoutP) {
            $xmlWriter->startElement('text:p'); // text:p
        }
        if ($element->getTrackChange() != null && $element->getTrackChange()->getChangeType() == TrackChange::DELETED) {
            $xmlWriter->startElement('text:change');
            $xmlWriter->writeAttribute('text:change-id', $element->getTrackChange()->getElementId());
            $xmlWriter->endElement();
        } else {
            if (empty($paragraphStyle)) {
                if (!$this->withoutP) {
                    $xmlWriter->writeAttribute('text:style-name', 'Normal');
                }
            } elseif (is_string($paragraphStyle)) {
                if (!$this->withoutP) {
                    $xmlWriter->writeAttribute('text:style-name', $paragraphStyle);
                }
            }

            if (!empty($fontStyle)) {
                // text:span
                $xmlWriter->startElement('text:span');
                if (is_string($fontStyle)) {
                    $xmlWriter->writeAttribute('text:style-name', $fontStyle);
                }
            }

            $this->writeChangeInsertion(true, $element->getTrackChange());
            $this->replaceTabs($element->getText(), $xmlWriter);
            $this->writeChangeInsertion(false, $element->getTrackChange());

            if (!empty($fontStyle)) {
                $xmlWriter->endElement();
            }
        }
        if (!$this->withoutP) {
            $xmlWriter->endElement(); // text:p
        }
    }

    private function writeChangeInsertion($start = true, ?TrackChange $trackChange = null): void
    {
        if ($trackChange == null || $trackChange->getChangeType() != TrackChange::INSERTED) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();
        $xmlWriter->startElement('text:change-' . ($start ? 'start' : 'end'));
        $xmlWriter->writeAttribute('text:change-id', $trackChange->getElementId());
        $xmlWriter->endElement();
    }
}
