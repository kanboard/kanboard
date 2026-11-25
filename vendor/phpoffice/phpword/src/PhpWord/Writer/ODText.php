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

use PhpOffice\Math\Writer\MathML;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Element\Formula;
use PhpOffice\PhpWord\Media;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Writer\ODText\Part\AbstractPart;

/**
 * ODText writer.
 *
 * @since 0.7.0
 */
class ODText extends AbstractWriter implements WriterInterface
{
    /**
     * @var AbstractElement[]
     */
    protected $objects = [];

    /**
     * Create new ODText writer.
     */
    public function __construct(?PhpWord $phpWord = null)
    {
        // Assign PhpWord
        $this->setPhpWord($phpWord);

        // Create parts
        $this->parts = [
            'Mimetype' => 'mimetype',
            'Content' => 'content.xml',
            'Meta' => 'meta.xml',
            'Styles' => 'styles.xml',
            'Manifest' => 'META-INF/manifest.xml',
        ];
        foreach (array_keys($this->parts) as $partName) {
            $partClass = static::class . '\\Part\\' . $partName;
            if (class_exists($partClass)) {
                /** @var AbstractPart $partObject Type hint */
                $partObject = new $partClass();
                $partObject->setParentWriter($this);
                $this->writerParts[strtolower($partName)] = $partObject;
            }
        }

        // Set package paths
        $this->mediaPaths = ['image' => 'Pictures/'];
    }

    /**
     * Save PhpWord to file.
     */
    public function save(string $filename): void
    {
        $filename = $this->getTempFile($filename);
        $zip = $this->getZipArchive($filename);

        // Add section media files
        $sectionMedia = Media::getElements('section');
        if (!empty($sectionMedia)) {
            $this->addFilesToPackage($zip, $sectionMedia);
        }

        // Write parts
        foreach ($this->parts as $partName => $fileName) {
            if ($fileName === '') {
                continue;
            }
            $part = $this->getWriterPart($partName);
            if (!$part instanceof AbstractPart) {
                continue;
            }

            $part->setObjects($this->objects);

            $zip->addFromString($fileName, $part->write());

            $this->objects = $part->getObjects();
        }

        // Write objects charts
        if (!empty($this->objects)) {
            $writer = new MathML();
            foreach ($this->objects as $idxObject => $object) {
                if ($object instanceof Formula) {
                    $zip->addFromString('Formula' . $idxObject . '/content.xml', $writer->write($object->getMath()));
                }
            }
        }

        // Close zip archive and cleanup temp file
        $zip->close();
        $this->cleanupTempFile();
    }
}
