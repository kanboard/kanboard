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

use PhpOffice\PhpWord\Element\TrackChange;

/**
 * Text element writer.
 *
 * @since 0.10.0
 */
class Text extends AbstractElement
{
    /**
     * Write text element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof \PhpOffice\PhpWord\Element\Text) {
            return;
        }

        $this->startElementP();

        $this->writeOpeningTrackChange();

        $xmlWriter->startElement('w:r');

        $this->writeFontStyle();

        $textElement = 'w:t';
        //'w:delText' in case of deleted text
        $changed = $element->getTrackChange();
        if ($changed != null && $changed->getChangeType() == TrackChange::DELETED) {
            $textElement = 'w:delText';
        }
        $xmlWriter->startElement($textElement);

        $xmlWriter->writeAttribute('xml:space', 'preserve');
        $this->writeText($this->getText($element->getText()));
        $xmlWriter->endElement();
        $xmlWriter->endElement(); // w:r

        $this->writeClosingTrackChange();

        $this->endElementP(); // w:p
    }

    /**
     * Write opening of changed element.
     */
    protected function writeOpeningTrackChange(): void
    {
        $changed = $this->getElement()->getTrackChange();
        if ($changed == null) {
            return;
        }

        $xmlWriter = $this->getXmlWriter();

        if (($changed->getChangeType() == TrackChange::INSERTED)) {
            $xmlWriter->startElement('w:ins');
        } elseif ($changed->getChangeType() == TrackChange::DELETED) {
            $xmlWriter->startElement('w:del');
        }
        $xmlWriter->writeAttribute('w:author', $changed->getAuthor());
        if ($changed->getDate() != null) {
            $xmlWriter->writeAttribute('w:date', $changed->getDate()->format('Y-m-d\TH:i:s\Z'));
        }
        $xmlWriter->writeAttribute('w:id', $this->getElement()->getElementId());
    }

    /**
     * Write ending.
     */
    protected function writeClosingTrackChange(): void
    {
        $changed = $this->getElement()->getTrackChange();
        if ($changed == null) {
            return;
        }

        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->endElement(); // w:ins|w:del
    }
}
