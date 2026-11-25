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

namespace PhpOffice\PhpWord\Writer\ODText\Part;

use PhpOffice\PhpWord\Element\Formula;
use PhpOffice\PhpWord\Media;
use PhpOffice\PhpWord\Writer\ODText;

/**
 * ODText manifest part writer: META-INF/manifest.xml.
 */
class Manifest extends AbstractPart
{
    /**
     * Write part.
     *
     * @return string
     */
    public function write()
    {
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElement('manifest:manifest');
        $xmlWriter->writeAttribute('manifest:version', '1.2');
        $xmlWriter->writeAttribute('xmlns:manifest', 'urn:oasis:names:tc:opendocument:xmlns:manifest:1.0');

        $xmlWriter->startElement('manifest:file-entry');
        $xmlWriter->writeAttribute('manifest:media-type', 'application/vnd.oasis.opendocument.text');
        $xmlWriter->writeAttribute('manifest:full-path', '/');
        $xmlWriter->writeAttribute('manifest:version', '1.2');
        $xmlWriter->endElement();

        // Parts
        foreach (['content.xml', 'meta.xml', 'styles.xml'] as $part) {
            $xmlWriter->startElement('manifest:file-entry');
            $xmlWriter->writeAttribute('manifest:media-type', 'text/xml');
            $xmlWriter->writeAttribute('manifest:full-path', $part);
            $xmlWriter->endElement();
        }

        // Media files
        $media = Media::getElements('section');
        foreach ($media as $medium) {
            if ($medium['type'] == 'image') {
                $xmlWriter->startElement('manifest:file-entry');
                $xmlWriter->writeAttribute('manifest:media-type', $medium['imageType']);
                $xmlWriter->writeAttribute('manifest:full-path', 'Pictures/' . $medium['target']);
                $xmlWriter->endElement();
            }
        }

        foreach ($this->getObjects() as $idxObject => $object) {
            if ($object instanceof Formula) {
                $xmlWriter->startElement('manifest:file-entry');
                $xmlWriter->writeAttribute('manifest:full-path', 'Formula' . $idxObject . '/content.xml');
                $xmlWriter->writeAttribute('manifest:media-type', 'text/xml');
                $xmlWriter->endElement();
                $xmlWriter->startElement('manifest:file-entry');
                $xmlWriter->writeAttribute('manifest:full-path', 'Formula' . $idxObject . '/');
                $xmlWriter->writeAttribute('manifest:version', '1.2');
                $xmlWriter->writeAttribute('manifest:media-type', 'application/vnd.oasis.opendocument.formula');
                $xmlWriter->endElement();
            }
        }

        $xmlWriter->endElement(); // manifest:manifest

        return $xmlWriter->getData();
    }
}
