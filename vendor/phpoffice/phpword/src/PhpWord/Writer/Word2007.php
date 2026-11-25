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

namespace PhpOffice\PhpWord\Writer;

use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Media;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\ZipArchive;

/**
 * Word2007 writer.
 */
class Word2007 extends AbstractWriter implements WriterInterface
{
    /**
     * Content types values.
     *
     * @var array
     */
    private $contentTypes = ['default' => [], 'override' => []];

    /**
     * Document relationship.
     *
     * @var array
     */
    private $relationships = [];

    /**
     * Create new Word2007 writer.
     */
    public function __construct(?PhpWord $phpWord = null)
    {
        // Assign PhpWord
        $this->setPhpWord($phpWord);

        // Create parts
        // The first four files need to be in this order for Mimetype detection to work
        $this->parts = [
            'ContentTypes' => '[Content_Types].xml',
            'Rels' => '_rels/.rels',
            'RelsDocument' => 'word/_rels/document.xml.rels',
            'Document' => 'word/document.xml',
            'DocPropsApp' => 'docProps/app.xml',
            'DocPropsCore' => 'docProps/core.xml',
            'DocPropsCustom' => 'docProps/custom.xml',
            'Comments' => 'word/comments.xml',
            'Styles' => 'word/styles.xml',
            'Numbering' => 'word/numbering.xml',
            'Settings' => 'word/settings.xml',
            'WebSettings' => 'word/webSettings.xml',
            'FontTable' => 'word/fontTable.xml',
            'Theme' => 'word/theme/theme1.xml',
            'RelsPart' => '',
            'Header' => '',
            'Footer' => '',
            'Footnotes' => '',
            'Endnotes' => '',
            'Chart' => '',
        ];
        foreach (array_keys($this->parts) as $partName) {
            $partClass = static::class . '\\Part\\' . $partName;
            if (class_exists($partClass)) {
                /** @var Word2007\Part\AbstractPart $part Type hint */
                $part = new $partClass();
                $part->setParentWriter($this);
                $this->writerParts[strtolower($partName)] = $part;
            }
        }

        // Set package paths
        $this->mediaPaths = ['image' => 'word/media/', 'object' => 'word/embeddings/'];
    }

    /**
     * Save document by name.
     */
    public function save(string $filename): void
    {
        $filename = $this->getTempFile($filename);
        $zip = $this->getZipArchive($filename);
        $phpWord = $this->getPhpWord();

        // Content types
        $this->contentTypes['default'] = [
            'rels' => 'application/vnd.openxmlformats-package.relationships+xml',
            'xml' => 'application/xml',
        ];

        // Add section media files
        $sectionMedia = Media::getElements('section');
        if (!empty($sectionMedia)) {
            $this->addFilesToPackage($zip, $sectionMedia);
            $this->registerContentTypes($sectionMedia);
            foreach ($sectionMedia as $element) {
                $this->relationships[] = $element;
            }
        }

        // Add header/footer media files & relations
        $this->addHeaderFooterMedia($zip, 'header');
        $this->addHeaderFooterMedia($zip, 'footer');

        // Add header/footer contents
        $rId = Media::countElements('section') + 6; //@see Rels::writeDocRels for 6 first elements
        $sections = $phpWord->getSections();
        foreach ($sections as $section) {
            $this->addHeaderFooterContent($section, $zip, 'header', $rId);
            $this->addHeaderFooterContent($section, $zip, 'footer', $rId);
        }

        $this->addNotes($zip, $rId, 'footnote');
        $this->addNotes($zip, $rId, 'endnote');
        $this->addComments($zip, $rId);
        $this->addChart($zip, $rId);

        // Write parts
        foreach ($this->parts as $partName => $fileName) {
            if ($fileName != '') {
                $zip->addFromString($fileName, $this->getWriterPart($partName)->write());
            }
        }

        // Close zip archive and cleanup temp file
        $zip->close();
        $this->cleanupTempFile();
    }

    /**
     * Get content types.
     *
     * @return array
     */
    public function getContentTypes()
    {
        return $this->contentTypes;
    }

    /**
     * Get content types.
     *
     * @return array
     */
    public function getRelationships()
    {
        return $this->relationships;
    }

    /**
     * Add header/footer media files, e.g. footer1.xml.rels.
     *
     * @param string $docPart
     */
    private function addHeaderFooterMedia(ZipArchive $zip, $docPart): void
    {
        $elements = Media::getElements($docPart);
        if (!empty($elements)) {
            foreach ($elements as $file => $media) {
                if (count($media) > 0) {
                    if (!empty($media)) {
                        $this->addFilesToPackage($zip, $media);
                        $this->registerContentTypes($media);
                    }

                    /** @var Word2007\Part\AbstractPart $writerPart Type hint */
                    $writerPart = $this->getWriterPart('relspart')->setMedia($media);
                    $zip->addFromString("word/_rels/{$file}.xml.rels", $writerPart->write());
                }
            }
        }
    }

    /**
     * Add header/footer content.
     *
     * @param string $elmType header|footer
     * @param int &$rId
     */
    private function addHeaderFooterContent(Section &$section, ZipArchive $zip, $elmType, &$rId): void
    {
        $getFunction = $elmType == 'header' ? 'getHeaders' : 'getFooters';
        $elmCount = ($section->getSectionId() - 1) * 3;
        $elements = $section->$getFunction();
        /** @var \PhpOffice\PhpWord\Element\AbstractElement $element Type hint */
        foreach ($elements as &$element) {
            ++$elmCount;
            $element->setRelationId(++$rId);
            $elmFile = "{$elmType}{$elmCount}.xml"; // e.g. footer1.xml
            $this->contentTypes['override']["/word/$elmFile"] = $elmType;
            $this->relationships[] = ['target' => $elmFile, 'type' => $elmType, 'rID' => $rId];

            /** @var Word2007\Part\AbstractPart $writerPart Type hint */
            $writerPart = $this->getWriterPart($elmType)->setElement($element);
            $zip->addFromString("word/$elmFile", $writerPart->write());
        }
    }

    /**
     * Add footnotes/endnotes.
     *
     * @param int &$rId
     * @param string $noteType
     */
    private function addNotes(ZipArchive $zip, &$rId, $noteType = 'footnote'): void
    {
        $phpWord = $this->getPhpWord();
        $noteType = ($noteType == 'endnote') ? 'endnote' : 'footnote';
        $partName = "{$noteType}s";
        $method = 'get' . $partName;
        $collection = $phpWord->$method();

        // Add footnotes media files, relations, and contents
        if ($collection->countItems() > 0) {
            $media = Media::getElements($noteType);
            $this->addFilesToPackage($zip, $media);
            $this->registerContentTypes($media);
            $this->contentTypes['override']["/word/{$partName}.xml"] = $partName;
            $this->relationships[] = ['target' => "{$partName}.xml", 'type' => $partName, 'rID' => ++$rId];

            // Write relationships file, e.g. word/_rels/footnotes.xml
            if (!empty($media)) {
                /** @var Word2007\Part\AbstractPart $writerPart Type hint */
                $writerPart = $this->getWriterPart('relspart')->setMedia($media);
                $zip->addFromString("word/_rels/{$partName}.xml.rels", $writerPart->write());
            }

            // Write content file, e.g. word/footnotes.xml
            $writerPart = $this->getWriterPart($partName)->setElements($collection->getItems());
            $zip->addFromString("word/{$partName}.xml", $writerPart->write());
        }
    }

    /**
     * Add comments.
     *
     * @param int &$rId
     */
    private function addComments(ZipArchive $zip, &$rId): void
    {
        $phpWord = $this->getPhpWord();
        $collection = $phpWord->getComments();
        $partName = 'comments';

        // Add comment relations and contents
        if ($collection->countItems() > 0) {
            $this->relationships[] = ['target' => "{$partName}.xml", 'type' => $partName, 'rID' => ++$rId];

            // Write content file, e.g. word/comments.xml
            $writerPart = $this->getWriterPart($partName)->setElements($collection->getItems());
            $zip->addFromString("word/{$partName}.xml", $writerPart->write());
        }
    }

    /**
     * Add chart.
     *
     * @param int &$rId
     */
    private function addChart(ZipArchive $zip, &$rId): void
    {
        $phpWord = $this->getPhpWord();

        $collection = $phpWord->getCharts();
        $index = 0;
        if ($collection->countItems() > 0) {
            /** @var \PhpOffice\PhpWord\Element\Chart $chart */
            foreach ($collection->getItems() as $chart) {
                ++$index;
                ++$rId;
                $filename = "charts/chart{$index}.xml";

                // ContentTypes.xml
                $this->contentTypes['override']["/word/{$filename}"] = 'chart';

                // word/_rels/document.xml.rel
                $this->relationships[] = ['target' => $filename, 'type' => 'chart', 'rID' => $rId];

                // word/charts/chartN.xml
                $chart->setRelationId($rId);
                $writerPart = $this->getWriterPart('Chart');
                $writerPart->setElement($chart);
                $zip->addFromString("word/{$filename}", $writerPart->write());
            }
        }
    }

    /**
     * Register content types for each media.
     *
     * @param array $media
     */
    private function registerContentTypes($media): void
    {
        foreach ($media as $medium) {
            $mediumType = $medium['type'];
            if ($mediumType == 'image') {
                $extension = $medium['imageExtension'];
                if (!isset($this->contentTypes['default'][$extension])) {
                    $this->contentTypes['default'][$extension] = $medium['imageType'];
                }
            } elseif ($mediumType == 'object') {
                if (!isset($this->contentTypes['default']['bin'])) {
                    $this->contentTypes['default']['bin'] = 'application/vnd.openxmlformats-officedocument.oleObject';
                }
            }
        }
    }
}
