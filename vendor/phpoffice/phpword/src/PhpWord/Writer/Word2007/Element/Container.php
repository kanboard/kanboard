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

use PhpOffice\PhpWord\Element\AbstractContainer as ContainerElement;
use PhpOffice\PhpWord\Element\AbstractElement as Element;
use PhpOffice\PhpWord\Element\TextBreak as TextBreakElement;
use PhpOffice\PhpWord\Shared\XMLWriter;

/**
 * Container element writer (section, textrun, header, footnote, cell, etc.).
 *
 * @since 0.11.0
 */
class Container extends AbstractElement
{
    /**
     * Namespace; Can't use __NAMESPACE__ in inherited class (ODText).
     *
     * @var string
     */
    protected $namespace = 'PhpOffice\\PhpWord\\Writer\\Word2007\\Element';

    /**
     * @var array<string>
     */
    protected $containerWithoutP = ['TextRun', 'Footnote', 'Endnote', 'ListItemRun'];

    /**
     * Write element.
     */
    public function write(): void
    {
        $container = $this->getElement();
        if (!$container instanceof ContainerElement) {
            return;
        }
        $containerClass = substr(get_class($container), strrpos(get_class($container), '\\') + 1);
        $withoutP = in_array($containerClass, $this->containerWithoutP);
        $xmlWriter = $this->getXmlWriter();

        // Loop through elements
        $elements = $container->getElements();
        $elementClass = '';
        foreach ($elements as $element) {
            $elementClass = $this->writeElement($xmlWriter, $element, $withoutP);
        }

        // Special case for Cell: They have to contain a w:p element at the end.
        // The $elementClass contains the last element name. If it's empty string
        // or Table, the last element is not w:p
        $writeLastTextBreak = ($containerClass == 'Cell') && ($elementClass == '' || $elementClass == 'Table');
        if ($writeLastTextBreak) {
            $writerClass = $this->namespace . '\\TextBreak';
            /** @var AbstractElement $writer Type hint */
            $writer = new $writerClass($xmlWriter, new TextBreakElement(), $withoutP);
            $writer->write();
        }
    }

    /**
     * Write individual element.
     */
    private function writeElement(XMLWriter $xmlWriter, Element $element, bool $withoutP): string
    {
        $elementClass = substr(get_class($element), strrpos(get_class($element), '\\') + 1);
        $writerClass = $this->namespace . '\\' . $elementClass;

        if (class_exists($writerClass)) {
            /** @var AbstractElement $writer Type hint */
            $writer = new $writerClass($xmlWriter, $element, $withoutP);
            $writer->setPart($this->getPart());
            $writer->write();
        }

        return $elementClass;
    }
}
