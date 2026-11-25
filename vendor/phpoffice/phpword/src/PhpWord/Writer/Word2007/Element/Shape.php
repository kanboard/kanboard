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

use PhpOffice\PhpWord\Element\Shape as ShapeElement;
use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\Style\Shape as ShapeStyle;
use PhpOffice\PhpWord\Writer\Word2007\Style\Shape as ShapeStyleWriter;

/**
 * Shape element writer.
 *
 * @since 0.12.0
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
class Shape extends AbstractElement
{
    /**
     * Write element.
     */
    public function write(): void
    {
        $xmlWriter = $this->getXmlWriter();
        $element = $this->getElement();
        if (!$element instanceof ShapeElement) {
            return;
        }

        $style = $element->getStyle();
        $styleWriter = new ShapeStyleWriter($xmlWriter, $style);

        $type = $element->getType();
        if ($type == 'rect' && $style->getRoundness() !== null) {
            $type = 'roundrect';
        }
        $method = "write{$type}";

        if (!$this->withoutP) {
            $xmlWriter->startElement('w:p');
        }
        $this->writeCommentRangeStart();

        $xmlWriter->startElement('w:r');
        $xmlWriter->startElement('w:pict');
        $xmlWriter->startElement("v:{$type}");

        // Element style
        if (method_exists($this, $method)) {
            $this->$method($xmlWriter, $style);
        }

        // Child style
        $styleWriter->write();

        $xmlWriter->endElement(); // v:$type
        $xmlWriter->endElement(); // w:pict
        $xmlWriter->endElement(); // w:r

        $this->endElementP(); // w:p
    }

    /**
     * Write arc.
     */
    private function writeArc(XMLWriter $xmlWriter, ShapeStyle $style): void
    {
        $points = $this->getPoints('arc', $style->getPoints());

        $xmlWriter->writeAttributeIf($points['start'] !== null, 'startAngle', $points['start']);
        $xmlWriter->writeAttributeIf($points['end'] !== null, 'endAngle', $points['end']);
    }

    /**
     * Write curve.
     */
    private function writeCurve(XMLWriter $xmlWriter, ShapeStyle $style): void
    {
        $points = $this->getPoints('curve', $style->getPoints());

        $this->writeLine($xmlWriter, $style);
        $xmlWriter->writeAttributeIf($points['point1'] !== null, 'control1', $points['point1']);
        $xmlWriter->writeAttributeIf($points['point2'] !== null, 'control2', $points['point2']);
    }

    /**
     * Write line.
     */
    private function writeLine(XMLWriter $xmlWriter, ShapeStyle $style): void
    {
        $points = $this->getPoints('line', $style->getPoints());

        $xmlWriter->writeAttributeIf($points['start'] !== null, 'from', $points['start']);
        $xmlWriter->writeAttributeIf($points['end'] !== null, 'to', $points['end']);
    }

    /**
     * Write polyline.
     */
    private function writePolyline(XMLWriter $xmlWriter, ShapeStyle $style): void
    {
        $xmlWriter->writeAttributeIf($style->getPoints() !== null, 'points', $style->getPoints());
    }

    /**
     * Write rectangle.
     */
    private function writeRoundRect(XMLWriter $xmlWriter, ShapeStyle $style): void
    {
        $xmlWriter->writeAttribute('arcsize', $style->getRoundness());
    }

    /**
     * Set points.
     *
     * @param string $type
     * @param string $value
     *
     * @return array
     */
    private function getPoints($type, $value)
    {
        $points = [];

        switch ($type) {
            case 'arc':
            case 'line':
                $points = explode(' ', $value);
                [$start, $end] = array_pad($points, 2, null);
                $points = ['start' => $start, 'end' => $end];

                break;
            case 'curve':
                $points = explode(' ', $value);
                [$start, $end, $point1, $point2] = array_pad($points, 4, null);
                $points = ['start' => $start, 'end' => $end, 'point1' => $point1, 'point2' => $point2];

                break;
        }

        return $points;
    }
}
