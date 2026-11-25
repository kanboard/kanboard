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

namespace PhpOffice\PhpWord\Reader\Word2007;

use DOMElement;
use PhpOffice\PhpWord\ComplexType\TrackChangesView;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\XMLReader;
use PhpOffice\PhpWord\Style\Language;

/**
 * Settings reader.
 *
 * @since 0.14.0
 */
class Settings extends AbstractPart
{
    /**
     * @var array<string>
     */
    private $booleanProperties = [
        'mirrorMargins',
        'hideSpellingErrors',
        'hideGrammaticalErrors',
        'trackRevisions',
        'doNotTrackMoves',
        'doNotTrackFormatting',
        'evenAndOddHeaders',
        'updateFields',
        'autoHyphenation',
        'doNotHyphenateCaps',
        'bookFoldPrinting',
    ];

    /**
     * Read settings.xml.
     */
    public function read(PhpWord $phpWord): void
    {
        $xmlReader = new XMLReader();
        $xmlReader->getDomFromZip($this->docFile, $this->xmlFile);

        $docSettings = $phpWord->getSettings();

        $nodes = $xmlReader->getElements('*');
        if ($nodes->length > 0) {
            foreach ($nodes as $node) {
                $name = str_replace('w:', '', $node->nodeName);
                $value = $xmlReader->getAttribute('w:val', $node);
                $method = 'set' . $name;

                if (in_array($name, $this->booleanProperties)) {
                    $docSettings->$method($value !== 'false');
                } elseif (method_exists($this, $method)) {
                    $this->$method($xmlReader, $phpWord, $node);
                } elseif (method_exists($docSettings, $method)) {
                    $docSettings->$method($value);
                }
            }
        }
    }

    /**
     * Sets the document Language.
     */
    protected function setThemeFontLang(XMLReader $xmlReader, PhpWord $phpWord, DOMElement $node): void
    {
        $val = $xmlReader->getAttribute('w:val', $node);
        $eastAsia = $xmlReader->getAttribute('w:eastAsia', $node);
        $bidi = $xmlReader->getAttribute('w:bidi', $node);

        $themeFontLang = new Language();
        $themeFontLang->setLatin($val);
        $themeFontLang->setEastAsia($eastAsia);
        $themeFontLang->setBidirectional($bidi);

        $phpWord->getSettings()->setThemeFontLang($themeFontLang);
    }

    /**
     * Sets the document protection.
     */
    protected function setDocumentProtection(XMLReader $xmlReader, PhpWord $phpWord, DOMElement $node): void
    {
        $documentProtection = $phpWord->getSettings()->getDocumentProtection();

        $edit = $xmlReader->getAttribute('w:edit', $node);
        if ($edit !== null) {
            $documentProtection->setEditing($edit);
        }
    }

    /**
     * Sets the proof state.
     */
    protected function setProofState(XMLReader $xmlReader, PhpWord $phpWord, DOMElement $node): void
    {
        $proofState = $phpWord->getSettings()->getProofState();

        $spelling = $xmlReader->getAttribute('w:spelling', $node);
        $grammar = $xmlReader->getAttribute('w:grammar', $node);

        if ($spelling !== null) {
            $proofState->setSpelling($spelling);
        }
        if ($grammar !== null) {
            $proofState->setGrammar($grammar);
        }
    }

    /**
     * Sets the proof state.
     */
    protected function setZoom(XMLReader $xmlReader, PhpWord $phpWord, DOMElement $node): void
    {
        $percent = $xmlReader->getAttribute('w:percent', $node);
        $val = $xmlReader->getAttribute('w:val', $node);

        if ($percent !== null || $val !== null) {
            $phpWord->getSettings()->setZoom($percent === null ? $val : $percent);
        }
    }

    /**
     * Set the Revision view.
     */
    protected function setRevisionView(XMLReader $xmlReader, PhpWord $phpWord, DOMElement $node): void
    {
        $revisionView = new TrackChangesView();
        $revisionView->setMarkup(filter_var($xmlReader->getAttribute('w:markup', $node), FILTER_VALIDATE_BOOLEAN));
        $revisionView->setComments($xmlReader->getAttribute('w:comments', $node));
        $revisionView->setInsDel(filter_var($xmlReader->getAttribute('w:insDel', $node), FILTER_VALIDATE_BOOLEAN));
        $revisionView->setFormatting(filter_var($xmlReader->getAttribute('w:formatting', $node), FILTER_VALIDATE_BOOLEAN));
        $revisionView->setInkAnnotations(filter_var($xmlReader->getAttribute('w:inkAnnotations', $node), FILTER_VALIDATE_BOOLEAN));
        $phpWord->getSettings()->setRevisionView($revisionView);
    }

    protected function setConsecutiveHyphenLimit(XMLReader $xmlReader, PhpWord $phpWord, DOMElement $node): void
    {
        $value = $xmlReader->getAttribute('w:val', $node);

        if ($value !== null) {
            $phpWord->getSettings()->setConsecutiveHyphenLimit($value);
        }
    }

    protected function setHyphenationZone(XMLReader $xmlReader, PhpWord $phpWord, DOMElement $node): void
    {
        $value = $xmlReader->getAttribute('w:val', $node);

        if ($value !== null) {
            $phpWord->getSettings()->setHyphenationZone($value);
        }
    }
}
