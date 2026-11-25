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

namespace PhpOffice\PhpWord\Metadata;

use PhpOffice\PhpWord\ComplexType\ProofState;
use PhpOffice\PhpWord\ComplexType\TrackChangesView;
use PhpOffice\PhpWord\SimpleType\Zoom;
use PhpOffice\PhpWord\Style\Language;

/**
 * Setting class.
 *
 * @since 0.14.0
 * @see  http://www.datypic.com/sc/ooxml/t-w_CT_Settings.html
 */
class Settings
{
    /**
     * Magnification Setting.
     *
     * @see  http://www.datypic.com/sc/ooxml/e-w_zoom-1.html
     *
     * @var mixed either integer, in which case it treated as a percent, or one of PhpOffice\PhpWord\SimpleType\Zoom
     */
    private $zoom = 100;

    /**
     * Mirror Page Margins.
     *
     * @see http://www.datypic.com/sc/ooxml/e-w_mirrorMargins-1.html
     *
     * @var bool
     */
    private $mirrorMargins;

    /**
     * Hide spelling errors.
     *
     * @var bool
     */
    private $hideSpellingErrors = false;

    /**
     * Hide grammatical errors.
     *
     * @var bool
     */
    private $hideGrammaticalErrors = false;

    /**
     * Visibility of Annotation Types.
     *
     * @var TrackChangesView
     */
    private $revisionView;

    /**
     * Track Revisions to Document.
     *
     * @var bool
     */
    private $trackRevisions = false;

    /**
     * Do Not Use Move Syntax When Tracking Revisions.
     *
     * @var bool
     */
    private $doNotTrackMoves = false;

    /**
     * Do Not Track Formatting Revisions When Tracking Revisions.
     *
     * @var bool
     */
    private $doNotTrackFormatting = false;

    /**
     * Spelling and Grammatical Checking State.
     *
     * @var ProofState
     */
    private $proofState;

    /**
     * Document Editing Restrictions.
     *
     * @var Protection
     */
    private $documentProtection;

    /**
     * Enables different header for odd and even pages.
     *
     * @var bool
     */
    private $evenAndOddHeaders = false;

    /**
     * Theme Font Languages.
     *
     * @var ?Language
     */
    private $themeFontLang;

    /**
     * Automatically Recalculate Fields on Open.
     *
     * @var bool
     */
    private $updateFields = false;

    /**
     * Radix Point for Field Code Evaluation.
     *
     * @var string
     */
    private $decimalSymbol = '.';

    /**
     * Automatically hyphenate document contents when displayed.
     *
     * @var null|bool
     */
    private $autoHyphenation;

    /**
     * Maximum number of consecutively hyphenated lines.
     *
     * @var null|int
     */
    private $consecutiveHyphenLimit;

    /**
     * The allowed amount of whitespace before hyphenation is applied.
     *
     * @var null|float|int
     */
    private $hyphenationZone;

    /**
     * Do not hyphenate words in all capital letters.
     *
     * @var null|bool
     */
    private $doNotHyphenateCaps;

    /**
     * Enable or disable book-folded printing.
     *
     * @var bool
     */
    private $bookFoldPrinting = false;

    /**
     * @return Protection
     */
    public function getDocumentProtection()
    {
        if ($this->documentProtection == null) {
            $this->documentProtection = new Protection();
        }

        return $this->documentProtection;
    }

    /**
     * @param Protection $documentProtection
     */
    public function setDocumentProtection($documentProtection): void
    {
        $this->documentProtection = $documentProtection;
    }

    /**
     * @return ProofState
     */
    public function getProofState()
    {
        if ($this->proofState == null) {
            $this->proofState = new ProofState();
        }

        return $this->proofState;
    }

    /**
     * @param ProofState $proofState
     */
    public function setProofState($proofState): void
    {
        $this->proofState = $proofState;
    }

    /**
     * Are spelling errors hidden.
     *
     * @return bool
     */
    public function hasHideSpellingErrors()
    {
        return $this->hideSpellingErrors;
    }

    /**
     * Hide spelling errors.
     *
     * @param ?bool $hideSpellingErrors
     */
    public function setHideSpellingErrors($hideSpellingErrors): void
    {
        $this->hideSpellingErrors = $hideSpellingErrors === null ? true : $hideSpellingErrors;
    }

    /**
     * Are grammatical errors hidden.
     *
     * @return bool
     */
    public function hasHideGrammaticalErrors()
    {
        return $this->hideGrammaticalErrors;
    }

    /**
     * Hide grammatical errors.
     *
     * @param ?bool $hideGrammaticalErrors
     */
    public function setHideGrammaticalErrors($hideGrammaticalErrors): void
    {
        $this->hideGrammaticalErrors = $hideGrammaticalErrors === null ? true : $hideGrammaticalErrors;
    }

    /**
     * @return bool
     */
    public function hasEvenAndOddHeaders()
    {
        return $this->evenAndOddHeaders;
    }

    /**
     * @param ?bool $evenAndOddHeaders
     */
    public function setEvenAndOddHeaders($evenAndOddHeaders): void
    {
        $this->evenAndOddHeaders = $evenAndOddHeaders === null ? true : $evenAndOddHeaders;
    }

    /**
     * Get the Visibility of Annotation Types.
     *
     * @return TrackChangesView
     */
    public function getRevisionView()
    {
        return $this->revisionView;
    }

    /**
     * Set the Visibility of Annotation Types.
     */
    public function setRevisionView(?TrackChangesView $trackChangesView = null): void
    {
        $this->revisionView = $trackChangesView;
    }

    /**
     * @return bool
     */
    public function hasTrackRevisions()
    {
        return $this->trackRevisions;
    }

    /**
     * @param ?bool $trackRevisions
     */
    public function setTrackRevisions($trackRevisions): void
    {
        $this->trackRevisions = $trackRevisions === null ? true : $trackRevisions;
    }

    /**
     * @return bool
     */
    public function hasDoNotTrackMoves()
    {
        return $this->doNotTrackMoves;
    }

    /**
     * @param ?bool $doNotTrackMoves
     */
    public function setDoNotTrackMoves($doNotTrackMoves): void
    {
        $this->doNotTrackMoves = $doNotTrackMoves === null ? true : $doNotTrackMoves;
    }

    /**
     * @return bool
     */
    public function hasDoNotTrackFormatting()
    {
        return $this->doNotTrackFormatting;
    }

    /**
     * @param ?bool $doNotTrackFormatting
     */
    public function setDoNotTrackFormatting($doNotTrackFormatting): void
    {
        $this->doNotTrackFormatting = $doNotTrackFormatting === null ? true : $doNotTrackFormatting;
    }

    /**
     * @return mixed
     */
    public function getZoom()
    {
        return $this->zoom;
    }

    /**
     * @param mixed $zoom
     */
    public function setZoom($zoom): void
    {
        if (is_numeric($zoom)) {
            // zoom is a percentage
            $this->zoom = $zoom;
        } else {
            Zoom::validate($zoom);
            $this->zoom = $zoom;
        }
    }

    /**
     * @return bool
     */
    public function hasMirrorMargins()
    {
        return $this->mirrorMargins;
    }

    /**
     * @param bool $mirrorMargins
     */
    public function setMirrorMargins($mirrorMargins): void
    {
        $this->mirrorMargins = $mirrorMargins;
    }

    /**
     * Returns the Language.
     */
    public function getThemeFontLang(): ?Language
    {
        return $this->themeFontLang;
    }

    /**
     * Sets the Language for this document.
     */
    public function setThemeFontLang(Language $themeFontLang): self
    {
        $this->themeFontLang = $themeFontLang;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasUpdateFields()
    {
        return $this->updateFields;
    }

    /**
     * @param ?bool $updateFields
     */
    public function setUpdateFields($updateFields): void
    {
        $this->updateFields = $updateFields === null ? false : $updateFields;
    }

    /**
     * Returns the Radix Point for Field Code Evaluation.
     *
     * @return string
     */
    public function getDecimalSymbol()
    {
        return $this->decimalSymbol;
    }

    /**
     * sets the Radix Point for Field Code Evaluation.
     *
     * @param string $decimalSymbol
     */
    public function setDecimalSymbol($decimalSymbol): void
    {
        $this->decimalSymbol = $decimalSymbol;
    }

    /**
     * @return null|bool
     */
    public function hasAutoHyphenation()
    {
        return $this->autoHyphenation;
    }

    /**
     * @param bool $autoHyphenation
     */
    public function setAutoHyphenation($autoHyphenation): void
    {
        $this->autoHyphenation = (bool) $autoHyphenation;
    }

    /**
     * @return null|int
     */
    public function getConsecutiveHyphenLimit()
    {
        return $this->consecutiveHyphenLimit;
    }

    /**
     * @param int $consecutiveHyphenLimit
     */
    public function setConsecutiveHyphenLimit($consecutiveHyphenLimit): void
    {
        $this->consecutiveHyphenLimit = (int) $consecutiveHyphenLimit;
    }

    /**
     * @return null|float|int
     */
    public function getHyphenationZone()
    {
        return $this->hyphenationZone;
    }

    /**
     * @param null|float|int $hyphenationZone Measurement unit is twip
     */
    public function setHyphenationZone($hyphenationZone): void
    {
        $this->hyphenationZone = $hyphenationZone;
    }

    /**
     * @return null|bool
     */
    public function hasDoNotHyphenateCaps()
    {
        return $this->doNotHyphenateCaps;
    }

    /**
     * @param bool $doNotHyphenateCaps
     */
    public function setDoNotHyphenateCaps($doNotHyphenateCaps): void
    {
        $this->doNotHyphenateCaps = (bool) $doNotHyphenateCaps;
    }

    public function hasBookFoldPrinting(): bool
    {
        return $this->bookFoldPrinting;
    }

    public function setBookFoldPrinting(bool $bookFoldPrinting): self
    {
        $this->bookFoldPrinting = $bookFoldPrinting;

        return $this;
    }
}
