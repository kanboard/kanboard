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

namespace PhpOffice\PhpWord\Writer\HTML\Element;

use PhpOffice\PhpWord\Element\AbstractContainer as ContainerElement;

/**
 * Container element HTML writer.
 *
 * @since 0.11.0
 */
class Container extends AbstractElement
{
    /**
     * Namespace; Can't use __NAMESPACE__ in inherited class (RTF).
     *
     * @var string
     */
    protected $namespace = 'PhpOffice\\PhpWord\\Writer\\HTML\\Element';

    /**
     * Write container.
     *
     * @return string
     */
    public function write()
    {
        $container = $this->element;
        if (!$container instanceof ContainerElement) {
            return '';
        }
        $containerClass = substr(get_class($container), strrpos(get_class($container), '\\') + 1);
        $withoutP = in_array($containerClass, ['TextRun', 'Footnote', 'Endnote']) ? true : false;
        $content = '';

        $elements = $container->getElements();
        foreach ($elements as $element) {
            $elementClass = get_class($element);
            $writerClass = str_replace('PhpOffice\\PhpWord\\Element', $this->namespace, $elementClass);
            if (class_exists($writerClass)) {
                /** @var AbstractElement $writer Type hint */
                $writer = new $writerClass($this->parentWriter, $element, $withoutP);
                $content .= $writer->write();
            }
        }

        return $content;
    }
}
