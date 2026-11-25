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

namespace PhpOffice\PhpWord\Writer\HTML\Part;

use PhpOffice\PhpWord\Writer\HTML\Element\Container;
use PhpOffice\PhpWord\Writer\HTML\Element\TextRun as TextRunWriter;
use PhpOffice\PhpWord\Writer\PDF\TCPDF;

/**
 * HTML body part writer.
 *
 * @since 0.11.0
 */
class Body extends AbstractPart
{
    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $phpWord = $this->getParentWriter()->getPhpWord();

        $content = '';

        $content .= '<body>' . PHP_EOL;
        $sections = $phpWord->getSections();
        $secno = 0;
        $isTCPDFWriter = $this->getParentWriter() instanceof TCPDF;
        foreach ($sections as $section) {
            ++$secno;
            if ($isTCPDFWriter && $secno > 1) {
                $content .= "<div style=\"page: page$secno; page-break-before:always;\">" . PHP_EOL;
            } else {
                $content .= "<div style='page: page$secno'>" . PHP_EOL;
            }
            $writer = new Container($this->getParentWriter(), $section);
            $content .= $writer->write();
            $content .= '</div>' . PHP_EOL;
        }

        $content .= $this->writeNotes();
        $content .= '</body>' . PHP_EOL;

        return $content;
    }

    /**
     * Write footnote/endnote contents as textruns.
     *
     * @return string
     */
    private function writeNotes()
    {
        /** @var \PhpOffice\PhpWord\Writer\HTML $parentWriter Type hint */
        $parentWriter = $this->getParentWriter();
        $phpWord = $parentWriter->getPhpWord();
        $notes = $parentWriter->getNotes();

        $content = '';

        if (!empty($notes)) {
            $content .= '<hr />' . PHP_EOL;
            foreach ($notes as $noteId => $noteMark) {
                [$noteType, $noteTypeId] = explode('-', $noteMark);
                $method = 'get' . ($noteType == 'endnote' ? 'Endnotes' : 'Footnotes');
                $collection = $phpWord->$method()->getItems();

                if (isset($collection[$noteTypeId])) {
                    $element = $collection[$noteTypeId];
                    $noteAnchor = "<a name=\"note-{$noteId}\" />";
                    $noteAnchor .= "<a href=\"#{$noteMark}\" class=\"NoteRef\"><sup>{$noteId}</sup></a>";

                    $writer = new TextRunWriter($this->getParentWriter(), $element);
                    $writer->setOpeningText($noteAnchor);
                    $content .= $writer->write();
                }
            }
        }

        return $content;
    }
}
