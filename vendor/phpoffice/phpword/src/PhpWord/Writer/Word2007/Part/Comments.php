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

use PhpOffice\PhpWord\Element\Comment;
use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\Writer\Word2007\Element\Container;

/**
 * Word2007 comments part writer: word/comments.xml.
 */
class Comments extends AbstractPart
{
    /**
     * Comments collection to be written.
     *
     * @var Comment[]
     */
    protected $elements;

    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
        $xmlWriter->startElement('w:comments');
        $xmlWriter->writeAttribute('xmlns:ve', 'http://schemas.openxmlformats.org/markup-compatibility/2006');
        $xmlWriter->writeAttribute('xmlns:o', 'urn:schemas-microsoft-com:office:office');
        $xmlWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xmlWriter->writeAttribute('xmlns:m', 'http://schemas.openxmlformats.org/officeDocument/2006/math');
        $xmlWriter->writeAttribute('xmlns:v', 'urn:schemas-microsoft-com:vml');
        $xmlWriter->writeAttribute('xmlns:wp', 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing');
        $xmlWriter->writeAttribute('xmlns:w10', 'urn:schemas-microsoft-com:office:word');
        $xmlWriter->writeAttribute('xmlns:w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xmlWriter->writeAttribute('xmlns:wne', 'http://schemas.microsoft.com/office/word/2006/wordml');

        if ($this->elements !== null) {
            foreach ($this->elements as $element) {
                if ($element instanceof Comment) {
                    $this->writeComment($xmlWriter, $element);
                }
            }
        }

        $xmlWriter->endElement(); // w:comments

        return $xmlWriter->getData();
    }

    /**
     * Write comment item.
     */
    protected function writeComment(XMLWriter $xmlWriter, Comment $comment): void
    {
        $xmlWriter->startElement('w:comment');
        $xmlWriter->writeAttribute('w:id', $comment->getElementId());
        $xmlWriter->writeAttribute('w:author', $comment->getAuthor());
        if ($comment->getDate() != null) {
            $xmlWriter->writeAttribute('w:date', $comment->getDate()->format($this->dateFormat));
        }
        $xmlWriter->writeAttributeIf($comment->getInitials() != null, 'w:initials', $comment->getInitials());

        $containerWriter = new Container($xmlWriter, $comment);
        $containerWriter->write();

        $xmlWriter->endElement(); // w:comment
    }

    /**
     * Set element.
     *
     * @param Comment[] $elements
     *
     * @return self
     */
    public function setElements($elements)
    {
        $this->elements = $elements;

        return $this;
    }
}
